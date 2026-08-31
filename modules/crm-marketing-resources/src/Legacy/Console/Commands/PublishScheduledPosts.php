<?php

namespace App\Console\Commands;

use App\Models\ConnectedAccount;
use App\Models\SocialMediaPost;
use App\Services\FacebookService;
use App\Services\InstagramService;
use App\Services\LinkedInService;
use App\Services\TwitterService;
use App\Services\YouTubeService;
use App\Services\Zernio\ZernioPublisher;
use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;

class PublishScheduledPosts extends Command
{
    protected $signature = 'social-media:publish-scheduled';

    protected $description = 'Publish scheduled social media posts';

    public function handle(): void
    {
        $posts = SocialMediaPost::where('status', SocialMediaPost::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $this->info("Publishing post ID: {$post->id}");

            $zernio = app(ZernioPublisher::class);
            $mode = (string) config('services.zernio.mode', 'fallback');
            if (in_array($mode, ['preferred', 'zernio'], true) && $zernio->canPublishForPost($post)) {
                try {
                    $result = $zernio->publish($post, $post->platforms ?? []);
                    $ids = $post->platform_post_ids ?? [];
                    $ids['zernio'] = data_get($result, 'post._id');
                    $post->update(['platform_post_ids' => $ids, 'status' => SocialMediaPost::STATUS_PUBLISHED]);
                    $this->info("Post ID: {$post->id} published through Zernio");

                    continue;
                } catch (Exception $e) {
                    if ($mode === 'zernio') {
                        $post->markAsFailed();
                        $this->error("Failed to publish post ID: {$post->id} through Zernio. Error: {$e->getMessage()}");

                        continue;
                    }
                    $this->warn("Zernio publication failed for post ID: {$post->id}; using direct providers.");
                }
            }

            if ($mode === 'zernio') {
                $post->markAsFailed();
                $this->error("Post ID: {$post->id} has no complete Zernio account configuration.");

                continue;
            }

            $allSucceeded = true;

            foreach ($post->platforms as $platform) {
                try {
                    $platformName = is_string($platform) ? $platform : (string) ($platform['platform'] ?? '');
                    if ($platformName === '') {
                        throw new InvalidArgumentException('A social post target is missing its platform.');
                    }
                    $this->publishToPlatform($platformName, $post);
                    $this->info("Post ID: {$post->id} published to {$platformName}");
                } catch (Exception $e) {
                    $this->error("Failed to publish post ID: {$post->id} to {$platform}. Error: {$e->getMessage()}");
                    $allSucceeded = false;
                }
            }

            if ($allSucceeded) {
                $post->markAsPublished();
                $this->info("Post ID: {$post->id} published successfully");
            } else {
                $post->markAsFailed();
            }
        }

        $this->info('Finished publishing scheduled posts');
    }

    protected function publishToPlatform(string $platform, SocialMediaPost $post): void
    {
        switch ($platform) {
            case 'facebook':
                $service = app(FacebookService::class);
                $result = $service->publishPost($post->content);
                if ($result) {
                    $ids = $post->platform_post_ids ?? [];
                    $ids['facebook'] = $result['id'] ?? null;
                    $post->platform_post_ids = $ids;
                    $post->save();
                }
                break;

            case 'twitter':
                $account = ConnectedAccount::ofType('twitter')->primary()->first();
                if ($account) {
                    app(TwitterService::class)->postTweet($account, $post->content);
                }
                break;

            case 'instagram':
                $account = ConnectedAccount::ofType('instagram')->primary()->first();
                if ($account) {
                    app(InstagramService::class)->postMedia($account, $post->image, $post->content);
                }
                break;

            case 'linkedin':
                $account = ConnectedAccount::ofType('linkedin')->primary()->first();
                if ($account) {
                    app(LinkedInService::class)->sharePost($account, $post->content);
                }
                break;

            case 'youtube':
                $account = ConnectedAccount::ofType('youtube')->primary()->first();
                if ($account && $post->video) {
                    $videoPath = storage_path('app/public/'.$post->video);
                    $title = mb_substr(explode("\n", $post->content)[0], 0, 100);
                    $result = app(YouTubeService::class)->uploadVideo($account, $videoPath, $title, $post->content);
                    $ids = $post->platform_post_ids ?? [];
                    $ids['youtube'] = $result['id'] ?? null;
                    $post->platform_post_ids = $ids;
                    $post->save();
                }
                break;

            default:
                throw new Exception("Unsupported platform: {$platform}");
        }
    }
}
