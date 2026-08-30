<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Services\Zernio\ZernioAdvertisingService;
use App\Services\Zernio\ZernioClient;
use App\Services\Zernio\ZernioException;
use App\Services\Zernio\ZernioPublisher;
use App\Services\Zernio\ZernioTenantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class ZernioClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_authenticates_and_posts_using_zernio_contract(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        Http::fake(['https://zernio.com/*' => Http::response(['post' => ['_id' => 'post_1']], 201)]);
        $post = SocialMediaPost::factory()->create(['content' => 'Hello', 'platforms' => ['twitter']]);

        $result = app(ZernioPublisher::class)->publish($post, [['platform' => 'twitter', 'account_id' => 'account_1']]);

        self::assertSame('post_1', data_get($result, 'post._id'));
        Http::assertSent(fn ($request): bool => $request->url() === 'https://zernio.com/api/v1/posts'
            && $request->hasHeader('Authorization', 'Bearer sk_'.str_repeat('a', 64))
            && data_get($request->data(), 'platforms.0.accountId') === 'account_1');
    }

    public function test_missing_configuration_and_missing_account_fail_closed(): void
    {
        $post = SocialMediaPost::factory()->create(['platforms' => ['twitter']]);
        $this->expectException(ZernioException::class);
        app(ZernioPublisher::class)->publish($post, ['twitter']);
    }

    public function test_publisher_reports_when_every_target_has_an_account(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        self::assertTrue(app(ZernioPublisher::class)->canPublish([['platform' => 'twitter', 'account_id' => 'account_1']]));
        self::assertFalse(app(ZernioPublisher::class)->canPublish(['twitter']));
    }

    public function test_team_profile_is_provisioned_and_persisted(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        Http::fake([
            'https://zernio.com/api/v1/profiles' => Http::response([
                'profile' => ['_id' => 'profile_team_1'],
            ], 201),
        ]);

        $team = Team::factory()->create();
        $profileId = app(ZernioTenantService::class)->ensureProfile($team);

        self::assertSame('profile_team_1', $profileId);
        self::assertSame('profile_team_1', $team->fresh()->zernio_profile_id);
        Http::assertSent(fn ($request): bool => data_get($request->data(), 'name') === 'crm_team_'.$team->id);
    }

    public function test_team_profile_connect_rejects_unsupported_platform(): void
    {
        $team = Team::factory()->create(['zernio_profile_id' => 'profile_team_1']);

        $this->expectException(ZernioException::class);
        app(ZernioTenantService::class)->connectUrl($team, 'unknown');
    }

    public function test_team_post_rejects_an_account_from_another_profile(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        $team = Team::factory()->create(['zernio_profile_id' => 'profile_team_1']);
        Http::fake([
            'https://zernio.com/api/v1/accounts*' => Http::response([
                'accounts' => [['_id' => 'team_account', 'platform' => 'twitter']],
            ]),
        ]);
        $post = SocialMediaPost::factory()->create([
            'team_id' => $team->id,
            'platforms' => [['platform' => 'twitter', 'account_id' => 'other_team_account']],
        ]);

        $this->expectException(ZernioException::class);
        app(ZernioPublisher::class)->publish($post, $post->platforms);
    }

    public function test_team_analytics_and_advertising_always_override_global_profile(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        config()->set('services.zernio.profile_id', 'global-profile');
        $team = Team::factory()->create(['zernio_profile_id' => 'team-profile']);
        Http::fake([
            'https://zernio.com/api/v1/analytics*' => Http::response(['analytics' => []]),
            'https://zernio.com/api/v1/ads*' => Http::response(['ads' => []]),
        ]);

        app(ZernioTenantService::class)->analytics($team, ['profileId' => 'caller-profile']);
        app(ZernioAdvertisingService::class)->listAds($team, ['profileId' => 'caller-profile']);

        Http::assertSentCount(2);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'profileId=team-profile'));
        Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'global-profile') || str_contains($request->url(), 'caller-profile'));
    }

    public function test_inbox_reply_uses_the_team_profile_query(): void
    {
        config()->set('services.zernio.api_key', 'sk_'.str_repeat('a', 64));
        Http::fake(['https://zernio.com/api/v1/inbox/conversations/*' => Http::response(['message' => ['id' => 'message_1']], 201)]);

        app(ZernioClient::class)->sendInboxMessage('conversation_1', 'account_1', 'Reply', 'team-profile');

        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'profileId=team-profile'));
    }
}
