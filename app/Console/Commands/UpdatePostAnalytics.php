<?php

namespace App\Console\Commands;

use App\Models\ConnectedAccount;
use App\Models\SocialMediaPost;
use App\Services\FacebookService;
use App\Services\Zernio\ZernioTenantService;
use Exception;
use Illuminate\Console\Command;

class UpdatePostAnalytics extends Command
{
    protected $signature = 'social-media:update-analytics';

    protected $description = 'Update analytics for published social media posts';

    public function handle(): void
    {
        $posts = SocialMediaPost::where('status', SocialMediaPost::STATUS_PUBLISHED)
            ->where('updated_at', '<=', now()->subHours(1))
            ->get();

        foreach ($posts as $post) {
            $this->info("Updating analytics for post ID: {$post->id}");

            try {
                $analytics = [];

                if (isset($post->platform_post_ids['zernio'])) {
                    $analytics = $this->fetchZernioAnalytics($post);
                } else {
                    foreach ($post->platforms as $platform) {
                        $platformName = is_string($platform) ? $platform : (string) ($platform['platform'] ?? '');
                        $platformAnalytics = $this->fetchPlatformAnalytics($platformName, $post);
                        foreach ($platformAnalytics as $key => $value) {
                            $analytics[$key] = ($analytics[$key] ?? 0) + $value;
                        }
                    }
                }

                if ($analytics !== []) {
                    $post->update($analytics);
                }

                $this->info("Analytics updated for post ID: {$post->id}");
            } catch (Exception $e) {
                $this->error("Failed to update analytics for post ID: {$post->id}. Error: {$e->getMessage()}");
            }
        }

        $this->info('Finished updating post analytics');
    }

    protected function fetchPlatformAnalytics(string $platform, SocialMediaPost $post): array
    {
        $postIds = $post->platform_post_ids ?? [];

        switch ($platform) {
            case 'facebook':
                if (isset($postIds['facebook'])) {
                    // Resolve via the container (not `new`) so the client is swappable
                    // and matches PublishScheduledPosts; the ctor builds a live SDK client.
                    $service = app(FacebookService::class);
                    $insights = $service->getPostInsights($postIds['facebook']);

                    return [
                        'impressions' => $insights['post_impressions'] ?? 0,
                    ];
                }
                break;

            case 'twitter':
                $account = ConnectedAccount::ofType('twitter')->primary()->first();
                if ($account) {
                    // Twitter analytics via API requires elevated access; basic stub
                    return [];
                }
                break;

            case 'instagram':
                $account = ConnectedAccount::ofType('instagram')->primary()->first();
                if ($account) {
                    // Instagram insights require a Business account; basic stub
                    return [];
                }
                break;

            case 'linkedin':
                $account = ConnectedAccount::ofType('linkedin')->primary()->first();
                if ($account) {
                    // LinkedIn analytics via UGC Posts API; basic stub
                    return [];
                }
                break;

            case 'youtube':
                $account = ConnectedAccount::ofType('youtube')->primary()->first();
                if ($account && isset($postIds['youtube'])) {
                    // YouTube analytics via YouTube Analytics API; basic stub
                    return [];
                }
                break;
        }

        return [];
    }

    /** @return array<string, int> */
    private function fetchZernioAnalytics(SocialMediaPost $post): array
    {
        if ((string) config('services.zernio.api_key') === '') {
            return [];
        }

        $team = $post->team;
        if ($team === null) {
            return [];
        }

        $response = app(ZernioTenantService::class)->analytics($team, array_filter([
            'postId' => (string) data_get($post->platform_post_ids, 'zernio'),
        ]));

        return $this->normalizeZernioAnalytics($response);
    }

    /** @param array<string, mixed> $response @return array<string, int> */
    private function normalizeZernioAnalytics(array $response): array
    {
        $analytics = data_get($response, 'analytics', []);

        if (! is_array($analytics)) {
            return [];
        }

        return array_map(
            static fn (mixed $value): int => is_numeric($value) ? (int) $value : 0,
            array_intersect_key($analytics, array_flip([
                'likes', 'shares', 'comments', 'clicks', 'reach', 'impressions',
            ]))
        );
    }
}
