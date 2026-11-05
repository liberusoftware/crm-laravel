<?php

declare(strict_types=1);

namespace App\Services\Zernio;

use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Services\Marketing\PlatformConfiguration;
use Illuminate\Support\Facades\Storage;

final class ZernioPublisher
{
    public function __construct(
        private readonly ZernioClient $client,
        private readonly ZernioTenantService $tenants,
    ) {}

    /** @return array<string, mixed> */
    public function publish(SocialMediaPost $post, array $platforms): array
    {
        $targets = [];
        $allowedAccounts = $this->allowedAccounts($post);
        foreach ($platforms as $platform) {
            if (is_string($platform)) {
                $accountId = data_get(config('services.zernio.account_ids', []), $platform);
                $platformName = $platform;
            } else {
                $platformName = (string) ($platform['platform'] ?? '');
                $accountId = $platform['account_id'] ?? null;
            }

            if (! in_array($platformName, ZernioTenantService::PLATFORMS, true)) {
                throw new ZernioException("Unsupported Zernio platform: {$platformName}.");
            }

            if ($allowedAccounts !== null) {
                $owned = $allowedAccounts[$platformName] ?? [];
                $accountId = $accountId ?: (count($owned) === 1 ? $owned[0] : null);
                if (! is_string($accountId) || ! in_array($accountId, $owned, true)) {
                    throw new ZernioException("The Zernio account for {$platformName} is not connected to this team.");
                }
            }

            if ($platformName === '' || ! is_string($accountId) || $accountId === '') {
                throw new ZernioException("No Zernio account is configured for {$platformName}.");
            }

            $targets[] = ['platform' => $platformName, 'accountId' => $accountId];
        }

        $payload = [
            'content' => (string) $post->content,
            'platforms' => $targets,
            'mediaItems' => array_values(array_filter([
                filled($post->image_path) ? ['type' => 'image', 'url' => Storage::disk('public')->url($post->image_path)] : null,
                filled($post->video_url) ? ['type' => 'video', 'url' => $post->video_url] : null,
            ])),
        ];
        if ($post->scheduled_at !== null && $post->scheduled_at->isFuture()) {
            $payload['scheduledFor'] = $post->scheduled_at->toIso8601String();
            $payload['timezone'] = config('app.timezone', 'UTC');
        } else {
            $payload['publishNow'] = true;
        }
        if (filled($post->link)) {
            $payload['link'] = $post->link;
        }

        return $this->client->createPost($payload);
    }

    public function canPublishForPost(SocialMediaPost $post): bool
    {
        try {
            $allowed = $this->allowedAccounts($post);
            if ($allowed === null) {
                return $this->canPublish($post->platforms ?? []);
            }
            foreach ($post->platforms ?? [] as $platform) {
                $name = is_string($platform) ? $platform : (string) ($platform['platform'] ?? '');
                $id = is_array($platform) ? ($platform['account_id'] ?? null) : null;
                if (! isset($allowed[$name]) || ($id !== null ? ! in_array($id, $allowed[$name], true) : count($allowed[$name]) !== 1)) {
                    return false;
                }
            }

            return ($post->platforms ?? []) !== [];
        } catch (ZernioException) {
            return false;
        }
    }

    public function canPublish(array $platforms): bool
    {
        if ((string) app(PlatformConfiguration::class)->credential('zernio_api_key', config('services.zernio.api_key')) === '') {
            return false;
        }

        foreach ($platforms as $platform) {
            $platformName = is_string($platform) ? $platform : (string) ($platform['platform'] ?? '');
            $accountId = is_string($platform)
                ? data_get(config('services.zernio.account_ids', []), $platformName)
                : ($platform['account_id'] ?? null);
            if ($platformName === '' || ! is_string($accountId) || $accountId === '') {
                return false;
            }
        }

        return $platforms !== [];
    }

    /** @return array<string, list<string>>|null */
    private function allowedAccounts(SocialMediaPost $post): ?array
    {
        $teamId = $post->getAttribute('team_id');
        if (! $teamId || (string) app(PlatformConfiguration::class)->credential('zernio_api_key', config('services.zernio.api_key')) === '') {
            return null;
        }

        $team = Team::query()->findOrFail((int) $teamId);
        $accounts = $this->tenants->accounts($team);
        $accounts = data_get($accounts, 'accounts', data_get($accounts, 'data.accounts', []));
        if (! is_array($accounts)) {
            throw new ZernioException('Zernio returned an invalid account list.');
        }

        $allowed = [];
        foreach ($accounts as $account) {
            if (! is_array($account) || ! isset($account['platform'], $account['_id'])) {
                continue;
            }

            $allowed[(string) $account['platform']][] = (string) $account['_id'];
        }

        return $allowed;
    }
}
