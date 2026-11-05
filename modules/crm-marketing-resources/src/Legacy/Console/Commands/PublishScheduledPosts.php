<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SocialMediaPost;
use App\Services\Marketing\PostPublisher;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'social-media:publish-scheduled';

    protected $description = 'Publish approved, due posts through the selected workspace provider';

    public function handle(PostPublisher $publisher): int
    {
        SocialMediaPost::query()->where('status', 'scheduled')->where('scheduled_at', '<=', now())->chunkById(100, function ($posts) use ($publisher): void {
            foreach ($posts as $post) {
                try {
                    $publisher->publish($post);
                } catch (\Throwable $exception) {
                    report($exception);
                    $this->error('Could not process post '.$post->getKey().'.');
                }
            }
        });

        SocialMediaPost::query()->whereIn('status', ['publishing', 'publishing_unknown'])->whereNotNull('platform_post_ids->zernio')->chunkById(100, function ($posts) use ($publisher): void {
            foreach ($posts as $post) {
                try {
                    $publisher->refreshStatus($post);
                } catch (\Throwable $exception) {
                    report($exception);
                }
            }
        });

        return self::SUCCESS;
    }
}
