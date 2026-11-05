<?php

declare(strict_types=1);

namespace App\Services\Zernio;

use App\Services\Marketing\PlatformConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

final class ZernioTenantService
{
    /** @var list<string> */
    public const PLATFORMS = [
        'twitter', 'instagram', 'facebook', 'linkedin', 'tiktok', 'youtube',
        'pinterest', 'reddit', 'bluesky', 'threads', 'googlebusiness', 'telegram',
        'snapchat', 'whatsapp', 'discord', 'slack',
    ];

    public function __construct(private readonly ZernioClient $client) {}

    public function ensureProfile(Model $team): string
    {
        $existing = (string) $team->getAttribute('zernio_profile_id');
        if ($existing !== '') {
            return $existing;
        }

        $response = $this->client->createProfile(
            'crm_team_'.$team->getKey(),
            'Liberu CRM team '.$team->getKey(),
        );
        $profileId = (string) (data_get($response, 'profile._id')
            ?? data_get($response, 'profile.id')
            ?? data_get($response, 'data._id')
            ?? data_get($response, 'data.id')
            ?? data_get($response, '_id'));

        if ($profileId === '') {
            throw new ZernioException('Zernio did not return a profile identifier.');
        }

        $team->forceFill(['zernio_profile_id' => $profileId])->saveQuietly();

        return $profileId;
    }

    /** @return array<string, mixed> */
    public function connectUrl(Model $team, string $platform, ?string $redirectUrl = null): array
    {
        if (! in_array($platform, self::PLATFORMS, true)) {
            throw new ZernioException("Unsupported Zernio platform: {$platform}.");
        }

        return $this->client->getConnectUrl($platform, $this->ensureProfile($team), $redirectUrl);
    }

    /** @return array<string, mixed> */
    public function accounts(Model $team): array
    {
        return $this->client->listAccounts($this->ensureProfile($team));
    }

    /** @param array<string, mixed> $query @return array<string, mixed> */
    public function analytics(Model $team, array $query = []): array
    {
        $query['profileId'] = $this->ensureProfile($team);

        return $this->client->getAnalytics($query);
    }

    /** @return array<string, mixed> */
    public function inbox(Model $team, array $query = []): array
    {
        $query['profileId'] = $this->ensureProfile($team);

        return $this->client->listConversations($query);
    }

    /** @return array<string, mixed> */
    public function comments(Model $team, array $query = []): array
    {
        $query['profileId'] = $this->ensureProfile($team);

        return $this->client->listComments($query);
    }

    /** @return array<string, mixed> */
    public function reviews(Model $team, array $query = []): array
    {
        $query['profileId'] = $this->ensureProfile($team);

        return $this->client->listReviews($query);
    }

    /** @return array<string, mixed> */
    public function inboxAnalytics(Model $team, array $query = []): array
    {
        $query['profileId'] = $this->ensureProfile($team);

        return $this->client->getInboxAnalytics($query);
    }

    /** @return array<string, mixed> */
    public function replyToComment(Model $team, string $commentId, string $accountId, string $message): array
    {
        return $this->client->replyToComment($commentId, $accountId, $message, $this->ensureProfile($team));
    }

    public function provisionIfConfigured(Model $team): void
    {
        if (app(PlatformConfiguration::class)->provider() !== 'zernio' || (string) app(PlatformConfiguration::class)->credential('zernio_api_key', config('services.zernio.api_key')) === '') {
            return;
        }

        try {
            $this->ensureProfile($team);
        } catch (ZernioException|ConnectionException $exception) {
            Log::error('Unable to provision the team Zernio profile.', [
                'team_id' => $team->getKey(),
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
