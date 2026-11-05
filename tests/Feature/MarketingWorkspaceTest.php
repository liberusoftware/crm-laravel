<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Admin\Pages\SetupWizard as PlatformWizard;
use App\Filament\App\Pages\ContentCalendar;
use App\Filament\App\Pages\MarketingConnections;
use App\Filament\App\Pages\SetupWizard;
use App\Filament\App\Resources\AdvertisingAccountResource\Pages\CreateAdvertisingAccount;
use App\Filament\App\Resources\SocialMediaPostResource\Pages\CreateSocialMediaPost;
use App\Models\AdvertisingAccount;
use App\Models\Campaign;
use App\Models\MarketingPlatformSettings;
use App\Models\OAuthConfiguration;
use App\Models\SocialMediaPost;
use App\Models\User;
use App\Services\Marketing\AdvertisingManager;
use App\Services\Marketing\DirectPostPublisher;
use App\Services\Marketing\PlatformConfiguration;
use App\Services\Marketing\PostPublisher;
use App\Services\Marketing\PostWorkflow;
use App\Services\Marketing\WorkspaceOnboarding;
use Database\Seeders\RolesSeeder;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class MarketingWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $user = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($user->currentTeam);

        return $user;
    }

    public function test_workspace_setup_cannot_change_platform_credentials_even_with_tampered_input(): void
    {
        $user = $this->owner();
        MarketingPlatformSettings::query()->create(['id' => 1, 'provider' => 'zernio', 'credentials' => ['zernio_api_key' => 'platform-secret']]);
        app(WorkspaceOnboarding::class)->save($user, $user->currentTeam, [
            'account_name' => 'Workspace owner', 'account_email' => $user->email, 'team_name' => 'North Studio',
            'timezone' => 'Europe/London', 'brand_name' => 'North', 'audience' => 'Small businesses',
            'tone' => 'professional', 'platforms' => ['linkedin'], 'approval_required' => true,
            'provider' => 'direct', 'zernio_api_key' => 'attacker-value', 'credentials' => ['api_key' => 'injected'],
        ]);
        $this->assertSame('zernio', app(PlatformConfiguration::class)->provider());
        $this->assertSame('platform-secret', app(PlatformConfiguration::class)->credential('zernio_api_key'));
        $this->assertSame('Europe/London', $user->currentTeam->fresh()->marketing_preferences['timezone']);
        $this->assertNotNull($user->currentTeam->fresh()->setup_completed_at);
        $this->assertArrayNotHasKey('credentials', $user->currentTeam->fresh()->marketing_preferences);
    }

    public function test_end_users_cannot_open_or_save_platform_setup(): void
    {
        $user = $this->owner();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(PlatformWizard::class)->assertForbidden();
        $this->expectException(HttpException::class);
        app(PlatformConfiguration::class)->save($user, ['provider' => 'direct', 'ai_enabled' => false]);
    }

    public function test_platform_keys_are_encrypted_and_blank_updates_preserve_them(): void
    {
        $user = $this->owner();
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');
        $configuration = app(PlatformConfiguration::class);
        $configuration->save($user, ['provider' => 'zernio', 'ai_enabled' => false, 'zernio_api_key' => 'secret-key']);
        $configuration->save($user, ['provider' => 'zernio', 'ai_enabled' => false, 'zernio_api_key' => '']);
        $this->assertSame('secret-key', $configuration->credential('zernio_api_key'));
        $this->assertStringNotContainsString('secret-key', DB::table('marketing_platform_settings')->value('credentials'));
        $this->assertArrayNotHasKey('credentials', $configuration->settings()->toArray());
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(PlatformWizard::class)->assertOk()->assertSet('data.zernio_api_key', null);
    }

    public function test_scheduling_requires_approval_and_content_edits_invalidate_it(): void
    {
        $user = $this->owner();
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'platforms' => ['twitter']]);
        $workflow = app(PostWorkflow::class);
        $workflow->approve($user, $user->currentTeam, $post);
        $workflow->schedule($user, $user->currentTeam, $post, now()->addHour()->toIso8601String());
        $this->assertSame('scheduled', $post->status);
        $post->update(['content' => 'Edited after approval']);
        $this->assertNull($post->refresh()->approved_at);
        $this->expectException(HttpException::class);
        $workflow->schedule($user, $user->currentTeam, $post, now()->addHours(2)->toIso8601String());
    }

    public function test_due_zernio_posts_resolve_workspace_accounts_and_are_not_sent_twice(): void
    {
        $user = $this->owner();
        $user->currentTeam->forceFill(['zernio_profile_id' => 'profile-own'])->save();
        config(['services.zernio.mode' => 'zernio', 'services.zernio.api_key' => 'platform-key']);
        Http::fake([
            '*/accounts*' => Http::response(['accounts' => [['_id' => 'own-account', 'platform' => 'twitter']]]),
            '*/posts' => Http::response(['post' => ['_id' => 'remote-post', 'status' => 'published']], 201),
        ]);
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'platforms' => ['twitter'], 'status' => 'scheduled', 'approved_at' => now(), 'scheduled_at' => now()->subMinute()]);
        $publisher = app(PostPublisher::class);
        $publisher->publish($post);
        $publisher->publish($post->refresh());
        $this->assertSame('published', $post->refresh()->status);
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/posts') && $request['platforms'][0]['accountId'] === 'own-account');
    }

    public function test_uncertain_provider_delivery_is_not_retried_or_sent_directly(): void
    {
        $user = $this->owner();
        $user->currentTeam->forceFill(['zernio_profile_id' => 'profile-own'])->save();
        config(['services.zernio.mode' => 'zernio', 'services.zernio.api_key' => 'platform-key']);
        $attempts = 0;
        Http::fake([
            '*/accounts*' => Http::response(['accounts' => [['_id' => 'own-account', 'platform' => 'twitter']]]),
            '*/posts' => function () use (&$attempts) {
                $attempts++;
                throw new ConnectionException('Timeout');
            },
        ]);
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'platforms' => ['twitter'], 'status' => 'scheduled', 'approved_at' => now(), 'scheduled_at' => now()->subMinute()]);
        app(PostPublisher::class)->publish($post);
        $this->assertSame('publishing_unknown', $post->refresh()->status);
        app(PostPublisher::class)->publish($post);
        Http::assertSentCount(1);
        $this->assertSame(1, $attempts);
    }

    public function test_direct_publishing_uses_only_the_workspaces_token(): void
    {
        $other = User::factory()->withPersonalTeam()->create();
        $user = $this->owner();
        foreach ([[$other->currentTeam->id, 'foreign-token'], [$user->currentTeam->id, 'own-token']] as [$id, $token]) {
            $connection = new OAuthConfiguration();
            $connection->forceFill(['team_id' => $id, 'user_id' => $user->id, 'service_name' => 'twitter', 'is_active' => true, 'access_token' => $token])->save();
        }
        config(['services.zernio.mode' => 'direct']);
        Http::fake(['api.x.com/*' => Http::response(['data' => ['id' => 'tweet-1']], 201)]);
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'platforms' => ['twitter'], 'status' => 'scheduled', 'approved_at' => now(), 'scheduled_at' => now()->subMinute()]);
        app(PostPublisher::class)->publish($post);
        $this->assertSame('published', $post->refresh()->status);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer own-token'));
        Http::assertNotSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer foreign-token'));
    }

    public function test_marketing_api_does_not_accept_provider_or_approval_overrides(): void
    {
        $user = $this->owner();
        $response = $this->postJson('/api/v1/marketing/posts', ['content' => 'Draft', 'platforms' => ['twitter'], 'status' => 'published', 'approved_at' => now()->toIso8601String(), 'publishing_provider' => 'zernio', 'team_id' => 999]);
        $response->assertCreated()->assertJsonPath('status', 'draft')->assertJsonPath('team_id', $user->currentTeam->id)->assertJsonPath('approved_at', null);
    }

    public function test_foreign_workspace_posts_cannot_be_approved(): void
    {
        $user = $this->owner();
        $other = User::factory()->withPersonalTeam()->create();
        $post = SocialMediaPost::factory()->create(['team_id' => $other->currentTeam->id]);
        $this->postJson('/api/v1/marketing/posts/'.$post->id.'/approve')->assertNotFound();
    }

    public function test_first_login_routes_workspace_owners_and_super_admins_to_the_correct_wizard(): void
    {
        $user = $this->owner();
        $request = Request::create('/app/login', 'POST');
        $response = app(LoginResponse::class)->toResponse($request);
        $this->assertStringContainsString('/setup-wizard', $response->getTargetUrl());
        $this->assertStringContainsString('/app/', $response->getTargetUrl());
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $response = app(LoginResponse::class)->toResponse($request);
        $this->assertStringContainsString('/admin/', $response->getTargetUrl());
        $this->assertStringContainsString('/setup-wizard', $response->getTargetUrl());
    }

    public function test_ai_assistance_returns_reviewable_copy_without_creating_or_publishing_posts(): void
    {
        $this->owner();
        MarketingPlatformSettings::query()->create(['id' => 1, 'provider' => 'direct', 'options' => ['ai_enabled' => true], 'credentials' => ['ai_endpoint' => 'https://content.example.test/generate', 'ai_api_key' => 'content-key']]);
        Http::fake(['content.example.test/*' => Http::response(['content' => 'A useful new service for your team.', 'headline' => 'Meet your new workspace', 'call_to_action' => 'Learn more'])]);
        $this->postJson('/api/v1/marketing/generate', ['purpose' => 'advertisement', 'brief' => 'Introduce our CRM', 'platform' => 'linkedin'])->assertOk()->assertJsonPath('headline', 'Meet your new workspace');
        $this->assertDatabaseCount('social_media_posts', 0);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer content-key') && $request['require_human_review'] === true);
    }

    public function test_direct_campaign_creation_is_paused_and_cannot_be_replayed(): void
    {
        $user = $this->owner();
        config(['services.facebook.graph_version' => 'v23.0']);
        $account = AdvertisingAccount::factory()->create(['team_id' => $user->currentTeam->id, 'platform' => 'facebook', 'account_id' => 'act_123']);
        $campaign = Campaign::factory()->create(['team_id' => $user->currentTeam->id, 'advertising_account_id' => $account->id, 'external_id' => null, 'budget' => 12.34, 'budget_type' => 'daily']);
        Http::fake(['graph.facebook.com/*' => Http::response(['id' => 'remote-campaign'], 201)]);
        $manager = app(AdvertisingManager::class);
        $manager->execute($user, $user->currentTeam, $campaign, 'create');
        $this->assertSame('paused', $campaign->refresh()->status);
        $this->assertSame('remote-campaign', $campaign->external_id);
        Http::assertSent(fn ($request) => $request['status'] === 'PAUSED' && $request['daily_budget'] === 1234);
        $this->expectException(HttpException::class);
        $manager->execute($user, $user->currentTeam, $campaign, 'create');
    }

    public function test_direct_campaign_operations_verify_remote_account_ownership(): void
    {
        $user = $this->owner();
        config(['services.facebook.graph_version' => 'v23.0']);
        $account = AdvertisingAccount::factory()->create(['team_id' => $user->currentTeam->id, 'platform' => 'facebook', 'account_id' => 'act_123']);
        $campaign = Campaign::factory()->create(['team_id' => $user->currentTeam->id, 'advertising_account_id' => $account->id, 'external_id' => 'foreign-campaign']);
        Http::fake(['graph.facebook.com/*' => Http::response(['account_id' => '999'])]);
        $this->postJson('/api/v1/marketing/campaigns/'.$campaign->id.'/manage', ['operation' => 'activate'])->assertForbidden();
        Http::assertNotSent(fn ($request) => $request->method() === 'POST');
    }

    public function test_workspace_wizard_never_hydrates_platform_keys_and_calendar_renders(): void
    {
        $user = $this->owner();
        MarketingPlatformSettings::query()->create(['id' => 1, 'provider' => 'direct', 'credentials' => ['zernio_api_key' => 'hidden-platform-key']]);
        Livewire::test(SetupWizard::class)->assertOk()->assertDontSee('hidden-platform-key')->assertDontSee('Zernio API key');
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'content' => 'Our planned launch', 'scheduled_at' => now()->addDay()]);
        Livewire::test(ContentCalendar::class)->assertOk()->assertSee('Our planned launch');
        Livewire::test(MarketingConnections::class)->assertOk()->assertSee('No accounts connected yet.');
    }

    public function test_zernio_campaign_update_uses_scoped_ownership_put_and_whole_unit_budget(): void
    {
        $user = $this->owner();
        $user->currentTeam->forceFill(['zernio_profile_id' => 'own-profile'])->save();
        config(['services.zernio.mode' => 'zernio', 'services.zernio.api_key' => 'platform-key']);
        $account = AdvertisingAccount::factory()->create(['team_id' => $user->currentTeam->id, 'platform' => 'facebook', 'account_id' => 'act_123', 'metadata' => ['zernio_account_id' => 'own-connection']]);
        $campaign = Campaign::factory()->create(['team_id' => $user->currentTeam->id, 'advertising_account_id' => $account->id, 'external_id' => 'own-campaign', 'budget' => 12.34, 'budget_type' => 'daily']);
        Http::fake([
            '*/accounts*' => Http::response(['accounts' => [['_id' => 'own-connection', 'platform' => 'facebook']]]),
            '*/ads/campaigns?*' => Http::response(['campaigns' => [['platformCampaignId' => 'own-campaign']]]),
            '*/ads/campaigns/own-campaign' => Http::response(['updated' => true]),
        ]);
        app(AdvertisingManager::class)->execute($user, $user->currentTeam, $campaign, 'update');
        Http::assertSent(fn ($request) => $request->method() === 'PUT' && $request['budget']['amount'] === 12.34 && $request['budget']['type'] === 'daily');
        Http::assertSent(fn ($request) => $request->method() === 'GET' && str_contains($request->url(), 'profileId=own-profile') && str_contains($request->url(), 'campaignId=own-campaign'));
    }

    public function test_meta_destination_selection_uses_an_owned_page_token_and_keeps_it_encrypted(): void
    {
        $user = $this->owner();
        config(['services.facebook.graph_version' => 'v23.0']);
        $connection = new OAuthConfiguration();
        $connection->forceFill(['team_id' => $user->currentTeam->id, 'user_id' => $user->id, 'service_name' => 'facebook', 'is_active' => true, 'access_token' => 'user-token'])->save();
        Http::fake([
            '*/me?*' => Http::response(['id' => 'user-123']),
            '*/me/accounts?*' => Http::response(['data' => [['id' => '123', 'access_token' => 'owned-page-token', 'name' => 'Our Page']]]),
        ]);
        app(DirectPostPublisher::class)->configureDestination($connection, '123');
        $this->assertSame('owned-page-token', $connection->fresh()->access_token);
        $this->assertSame('123', $connection->fresh()->additional_settings['publishing_id']);
        $this->assertStringNotContainsString('owned-page-token', DB::table('oauth_configurations')->where('id', $connection->id)->value('access_token'));
        $this->assertArrayNotHasKey('access_token', $connection->fresh()->additional_settings);
    }

    public function test_zernio_drafts_select_explicit_owned_accounts_and_reject_foreign_accounts(): void
    {
        $user = $this->owner();
        $user->currentTeam->forceFill(['zernio_profile_id' => 'own-profile'])->save();
        config(['services.zernio.mode' => 'zernio', 'services.zernio.api_key' => 'platform-key']);
        Http::fake(['*/accounts*' => Http::response(['accounts' => [
            ['_id' => 'page-one', 'platform' => 'facebook'], ['_id' => 'page-two', 'platform' => 'facebook'],
        ]])]);
        $this->postJson('/api/v1/marketing/posts', ['content' => 'For the second Page', 'platforms' => [['platform' => 'facebook', 'account_id' => 'page-two']]])
            ->assertCreated()->assertJsonPath('platforms.0.account_id', 'page-two');
        $this->postJson('/api/v1/marketing/posts', ['content' => 'Cannot target this Page', 'platforms' => [['platform' => 'facebook', 'account_id' => 'foreign-page']]])->assertUnprocessable();
        $this->assertDatabaseCount('social_media_posts', 1);
    }

    public function test_new_composer_preview_and_draft_creation_work_without_an_existing_record(): void
    {
        $this->seed(RolesSeeder::class);
        $user = $this->owner();
        setPermissionsTeamId($user->currentTeam->id);
        $user->assignRole('admin');
        Livewire::test(CreateSocialMediaPost::class)
            ->fillForm(['content' => 'An unsaved draft preview', 'platforms' => ['twitter']])
            ->assertSee('An unsaved draft preview')
            ->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('social_media_posts', ['content' => 'An unsaved draft preview', 'team_id' => $user->currentTeam->id, 'status' => 'draft', 'approved_at' => null]);
    }

    public function test_publisher_does_not_use_an_approval_revoked_after_the_post_was_loaded(): void
    {
        $user = $this->owner();
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'status' => 'scheduled', 'approved_at' => now(), 'scheduled_at' => now()->subMinute(), 'platforms' => ['twitter']]);
        SocialMediaPost::query()->whereKey($post->id)->update(['approved_at' => null]);
        Http::fake();
        app(PostPublisher::class)->publish($post);
        Http::assertNothingSent();
        $this->assertSame('scheduled', $post->refresh()->status);
    }

    public function test_editing_a_stale_draft_cannot_overwrite_a_claimed_publication(): void
    {
        $user = $this->owner();
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'content' => 'Claimed original', 'platforms' => ['twitter']]);
        SocialMediaPost::query()->whereKey($post->id)->update(['status' => 'publishing']);
        try {
            app(PostWorkflow::class)->update($user, $user->currentTeam, $post, ['content' => 'Late edit', 'platforms' => ['twitter']]);
            $this->fail('A claimed publication must not be overwritten.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        $this->assertSame('Claimed original', $post->refresh()->content);
        $this->assertSame('publishing', $post->status);
    }

    public function test_scheduling_normalizes_explicit_timezone_offsets_to_the_application_clock(): void
    {
        $user = $this->owner();
        $post = SocialMediaPost::factory()->create(['team_id' => $user->currentTeam->id, 'platforms' => ['twitter'], 'approved_at' => now()]);
        $when = now()->addHours(2)->setTimezone('Asia/Kolkata');
        app(PostWorkflow::class)->schedule($user, $user->currentTeam, $post, $when->toIso8601String());
        $this->assertSame($when->timestamp, $post->refresh()->scheduled_at->timestamp);
    }

    public function test_advertising_account_setup_collects_network_fields_without_exposing_platform_secrets(): void
    {
        $this->seed(RolesSeeder::class);
        $user = $this->owner();
        setPermissionsTeamId($user->currentTeam->id);
        $user->assignRole('admin');
        Livewire::test(CreateAdvertisingAccount::class)
            ->fillForm(['name' => 'Our Meta account', 'platform' => 'facebook', 'account_id' => 'act_123', 'access_token' => 'our-ad-token'])
            ->call('create')->assertHasNoFormErrors();
        $account = AdvertisingAccount::query()->where('account_id', 'act_123')->firstOrFail();
        $this->assertSame('facebook', $account->platform);
        $this->assertSame('our-ad-token', $account->access_token);
        $this->assertSame((int) $user->currentTeam->id, (int) $account->team_id);
        $this->assertStringNotContainsString('our-ad-token', DB::table('advertising_accounts')->where('id', $account->id)->value('access_token'));
    }

    public function test_platform_setup_shows_missing_zernio_credentials_on_the_matching_form_field(): void
    {
        $user = $this->owner();
        Role::findOrCreate('super_admin', 'web');
        $user->assignRole('super_admin');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(PlatformWizard::class)->set('data.provider', 'zernio')->set('data.ai_enabled', false)
            ->call('save')->assertHasFormErrors(['zernio_api_key'])->assertSet('saved', false);
        $this->assertDatabaseCount('marketing_platform_settings', 0);
    }
}
