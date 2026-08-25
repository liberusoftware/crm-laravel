<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResources\Queries;

use Liberu\CRM\MarketingResources\Models\MarketingResource;

final class MarketingResourceQuery
{
    public function forTeam(int $teamId)
    {
        return MarketingResource::query()->where('team_id', $teamId)->with('events')->latest();
    }
}
