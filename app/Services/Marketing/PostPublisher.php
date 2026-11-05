<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Services\Zernio\ZernioClient;
use App\Services\Zernio\ZernioPublisher;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Throwable;

final class PostPublisher
{
    public function __construct(private readonly PlatformConfiguration $configuration, private readonly DirectPostPublisher $direct, private readonly ZernioPublisher $zernio) {}

    public function publish(SocialMediaPost $post): void
    {
        $team = Team::query()->findOrFail($post->getAttribute('team_id'));
        $preferences = $team->getAttribute('marketing_preferences') ?? [];
        if (($preferences['approval_required'] ?? true) && ! $post->getAttribute('approved_at')) {
            $post->update(['last_error' => 'A reviewer must approve this post before publication.']);

            return;
        }
        if ($post->getAttribute('scheduled_at')?->isFuture()) {
            return;
        }
        $provider = $post->getAttribute('publishing_provider') ?? $this->configuration->provider();
        if (SocialMediaPost::query()->whereKey($post->getKey())->where('status', 'scheduled')->when($preferences['approval_required'] ?? true, fn ($query) => $query->whereNotNull('approved_at'))->update(['status' => 'publishing', 'publishing_provider' => $provider, 'publishing_started_at' => now(), 'last_error' => null]) !== 1) {
            return;
        }
        try {
            Validator::make(array_replace($post->getAttributes(), ['platforms' => $post->platforms]), ['content' => ['required', 'string'], 'platforms' => ['required', 'array', 'min:1']])->validate();
            if ($provider === 'zernio') {
                $result = $this->zernio->publish($post->refresh(), $post->platforms);
                $id = data_get($result, 'post._id', data_get($result, 'post.id'));
                if (! is_string($id) || $id === '') {
                    throw new RuntimeException('Zernio did not return a post identifier. Reconcile the request before retrying.');
                }
                $status = data_get($result, 'post.status');
                $post->update(['platform_post_ids' => ['zernio' => $id], 'status' => $status === 'published' ? 'published' : 'publishing']);
            } else {
                $ids = $post->platform_post_ids ?? [];
                foreach ($post->platforms as $platform) {
                    if (! is_string($platform)) {
                        throw new RuntimeException('Direct publishing requires platform names.');
                    }
                    if (! isset($ids[$platform])) {
                        $ids[$platform] = $this->direct->publish($post, $platform);
                        $post->update(['platform_post_ids' => $ids]);
                    }
                }
                $post->markAsPublished();
            }
        } catch (Throwable $exception) {
            $post->update(['status' => 'publishing_unknown', 'last_error' => mb_substr($exception->getMessage(), 0, 2000)]);
            report($exception);
        }
    }

    public function refreshStatus(SocialMediaPost $post): void
    {
        $id = data_get($post->platform_post_ids, 'zernio');
        if (! is_string($id) || ! in_array($post->status, ['publishing', 'publishing_unknown'], true)) {
            return;
        }
        $team = Team::query()->findOrFail($post->getAttribute('team_id'));
        $result = app(ZernioClient::class)->getPost($id);
        $profile = data_get($result, 'post.profileId');
        if ($profile !== null && $profile !== $team->getAttribute('zernio_profile_id')) {
            throw new RuntimeException('The provider post does not belong to this workspace.');
        }
        $status = data_get($result, 'post.status');
        if (in_array($status, ['published', 'failed', 'cancelled'], true)) {
            $post->update(['status' => $status, 'last_error' => $status === 'failed' ? 'The provider reported a publication failure. Review the provider record before retrying.' : null]);
        }
    }
}
