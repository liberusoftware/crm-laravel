<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Queries;

use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;
use Liberu\CRM\RevenueLifecycle\Models\RevenueFallout;
use Liberu\CRM\RevenueLifecycle\Models\RevenueOrder;

final class RevenueQuery
{
    public function assets(int $teamId)
    {
        return RevenueAsset::query()->where('team_id', $teamId)->latest();
    }

    public function orders(int $teamId)
    {
        return RevenueOrder::query()->where('team_id', $teamId)->latest();
    }

    public function fallout(int $teamId)
    {
        return RevenueFallout::query()->where('team_id', $teamId)->where('status', 'open')->latest();
    }
}
