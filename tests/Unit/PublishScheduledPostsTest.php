<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\OAuthConfiguration;
use App\Models\SocialMediaPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublishScheduledPostsTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $platforms, array $extra = []): SocialMediaPost
    {
        config(['services.zernio.mode' => 'direct', 'services.facebook.graph_version' => 'v23.0', 'services.linkedin.api_version' => '202606']);
        $owner = User::factory()->withPersonalTeam()->create();
        foreach ($platforms as $platform) {
            if (! is_string($platform)) {
                continue;
            }
            $connection = new OAuthConfiguration();
            $connection->forceFill(['team_id' => $owner->currentTeam->id, 'user_id' => $owner->id, 'service_name' => $platform, 'is_active' => true, 'access_token' => 'own-token', 'additional_settings' => ['publishing_id' => $platform === 'linkedin' ? 'urn:li:organization:123' : '123']])->save();
        }

        return SocialMediaPost::create(array_merge([
            'team_id' => $owner->currentTeam->id, 'content' => "Line one\nMore detail", 'platforms' => $platforms,
            'status' => SocialMediaPost::STATUS_SCHEDULED, 'scheduled_at' => now()->subMinute(), 'approved_at' => now(),
        ], $extra));
    }

    public function test_publishes_facebook_post(): void
    {
        $post = $this->makePost(['facebook']);
        Http::fake(['graph.facebook.com/*' => Http::response(['id' => 'fb_123'], 201)]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame('fb_123', $post->fresh()->platform_post_ids['facebook']);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer own-token') && $request['message'] === $post->content);
    }

    public function test_publishes_twitter_post(): void
    {
        $post = $this->makePost(['twitter']);
        Http::fake(['api.x.com/2/tweets' => Http::response(['data' => ['id' => 'tweet_123']], 201)]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame('tweet_123', $post->fresh()->platform_post_ids['twitter']);
    }

    public function test_publishes_instagram_post(): void
    {
        $post = $this->makePost(['instagram'], ['image_path' => 'posts/img.jpg']);
        Http::fake([
            '*/123/media' => Http::response(['id' => 'container_123'], 201),
            '*/123/media_publish' => Http::response(['id' => 'ig_123'], 201),
        ]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame('ig_123', $post->fresh()->platform_post_ids['instagram']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/media_publish') && $request['creation_id'] === 'container_123');
    }

    public function test_publishes_linkedin_post(): void
    {
        $post = $this->makePost(['linkedin']);
        Http::fake(['api.linkedin.com/rest/posts' => Http::response([], 201, ['x-restli-id' => 'urn:li:share:123'])]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $post->fresh()->status);
        Http::assertSent(fn ($request) => $request['author'] === 'urn:li:organization:123' && $request->hasHeader('LinkedIn-Version', '202606'));
    }

    public function test_unsupported_native_media_is_not_reported_as_published(): void
    {
        $post = $this->makePost(['youtube']);
        Http::fake();
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('publishing_unknown', $post->fresh()->status);
        $this->assertStringContainsString('not available', $post->fresh()->last_error);
        Http::assertNothingSent();
    }

    public function test_provider_failure_does_not_report_success_or_trigger_another_provider(): void
    {
        $post = $this->makePost(['facebook']);
        Http::fake(['graph.facebook.com/*' => Http::response(['error' => 'Unavailable'], 503)]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('publishing_unknown', $post->fresh()->status);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        Http::assertSentCount(1);
    }

    public function test_publishes_to_multiple_platforms_and_retains_each_identifier(): void
    {
        $post = $this->makePost(['facebook', 'twitter']);
        Http::fake([
            'graph.facebook.com/*' => Http::response(['id' => 'fb_1'], 201),
            'api.x.com/*' => Http::response(['data' => ['id' => 'tweet_1']], 201),
        ]);
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('published', $post->fresh()->status);
        $this->assertSame(['facebook' => 'fb_1', 'twitter' => 'tweet_1'], $post->fresh()->platform_post_ids);
    }

    public function test_unapproved_posts_are_not_sent(): void
    {
        $post = $this->makePost(['twitter'], ['approved_at' => null]);
        Http::fake();
        $this->artisan('social-media:publish-scheduled')->assertSuccessful();
        $this->assertSame('scheduled', $post->fresh()->status);
        Http::assertNothingSent();
    }
}
