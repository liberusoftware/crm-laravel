<?php

declare(strict_types=1);

namespace App\Services\Zernio;

use Illuminate\Database\Eloquent\Model;

final class ZernioAdvertisingService
{
    public function __construct(private readonly ZernioClient $client) {}

    /** @return array<string, mixed> */
    public function listAds(Model $team, array $filters = []): array
    {
        return $this->client->listAds($this->withProfile($team, $filters));
    }

    /** @return array<string, mixed> */
    public function campaignAnalytics(Model $team, string $campaignId, array $filters = []): array
    {
        return $this->client->getCampaignAnalytics($campaignId, $this->withProfile($team, $filters));
    }

    /** @return array<string, mixed> */
    public function adAnalytics(Model $team, string $adId, array $filters = []): array
    {
        return $this->client->getAdAnalytics($adId, $this->withProfile($team, $filters));
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function createCampaign(Model $team, array $payload): array
    {
        return $this->client->createCampaign(array_merge($payload, ['profileId' => app(ZernioTenantService::class)->ensureProfile($team)]));
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    public function updateCampaign(Model $team, string $campaignId, array $payload): array
    {
        $payload['profileId'] = app(ZernioTenantService::class)->ensureProfile($team);

        return $this->client->updateCampaign($campaignId, $payload);
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    private function withProfile(Model $team, array $filters): array
    {
        $filters['profileId'] = app(ZernioTenantService::class)->ensureProfile($team);

        return $filters;
    }
}
