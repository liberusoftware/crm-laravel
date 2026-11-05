<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\OAuthConfiguration;
use App\Models\SocialMediaPost;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminderNotification;
use App\Services\FacebookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_scheduled_posts_flips_due_post_to_published(): void
    {
        config(['services.zernio.mode' => 'direct', 'services.facebook.graph_version' => 'v23.0']);
        $owner = User::factory()->withPersonalTeam()->create();
        $connection = new OAuthConfiguration();
        $connection->forceFill(['team_id' => $owner->currentTeam->id, 'user_id' => $owner->id, 'service_name' => 'facebook', 'is_active' => true, 'access_token' => 'page-token', 'additional_settings' => ['publishing_id' => '123']])->save();
        Http::fake(['graph.facebook.com/*' => Http::response(['id' => 'fb-post-123'], 201)]);

        $post = SocialMediaPost::factory()->create([
            'team_id' => $owner->currentTeam->id,
            'approved_at' => now(),
            'status' => SocialMediaPost::STATUS_SCHEDULED,
            'scheduled_at' => now()->subMinutes(10),
            'platforms' => ['facebook'],
            'content' => 'Hello world',
        ]);

        $this->artisan('social-media:publish-scheduled')->assertSuccessful();

        $post->refresh();
        $this->assertSame(SocialMediaPost::STATUS_PUBLISHED, $post->status);
        $this->assertSame('fb-post-123', $post->platform_post_ids['facebook']);
    }

    public function test_send_reminders_notifies_assignee_and_flags_task(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        // reminderDue() = reminder_date in the past + reminder_sent false.
        $task = Task::factory()->reminderDue()->create([
            'assigned_to' => $user->id,
        ]);

        $this->artisan('reminders:send')->assertSuccessful();

        Notification::assertSentTo($user, TaskReminderNotification::class);
        $this->assertTrue($task->fresh()->reminder_sent);
    }

    public function test_update_post_analytics_writes_metrics_for_published_post(): void
    {
        // Mock the Facebook client so getPostInsights returns canned data.
        $this->mock(FacebookService::class)
            ->shouldReceive('getPostInsights')
            ->once()
            ->andReturn(['post_impressions' => 42]);

        $post = SocialMediaPost::factory()->create([
            'status' => SocialMediaPost::STATUS_PUBLISHED,
            'platforms' => ['facebook'],
            'platform_post_ids' => ['facebook' => 'fb-post-123'],
            'impressions' => 0,
        ]);

        // The command only touches posts untouched for >1h; age the row past that gate.
        DB::table('social_media_posts')
            ->where('id', $post->id)
            ->update(['updated_at' => now()->subHours(2)]);

        $this->artisan('social-media:update-analytics')->assertSuccessful();

        $this->assertSame(42, $post->fresh()->impressions);
    }
}
