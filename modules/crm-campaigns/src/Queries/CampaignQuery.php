<?php

declare(strict_types=1);

namespace Liberu\CRM\Campaigns\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Campaigns\Models\Campaign;

final class CampaignQuery
{
    public function campaigns(int $teamId): Builder
    {
        return Campaign::query()->where('team_id', $teamId)->latest();
    }

    public function roi(int $teamId): array
    {
        return $this->campaigns($teamId)->get()->mapWithKeys(fn ($campaign): array => [(string) $campaign->getKey() => ['revenue' => $campaign->getAttribute('revenue'), 'cost' => $campaign->getAttribute('cost'), 'roi' => (float) $campaign->getAttribute('cost') > 0 ? round(((float) $campaign->getAttribute('revenue') - (float) $campaign->getAttribute('cost')) / (float) $campaign->getAttribute('cost'), 4) : null]])->all();
    }
}
