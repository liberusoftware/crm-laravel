<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Queries;

use Liberu\CRM\RevenueIntelligence\Models\RevenueInsight;
use Liberu\CRM\RevenueIntelligence\Models\RevenueIntelligenceAlert;

final class RevenueIntelligenceQuery
{
    public function insights(int $teamId)
    {
        return RevenueInsight::query()->where('team_id', $teamId)->latest();
    }

    public function alerts(int $teamId)
    {
        return RevenueIntelligenceAlert::query()->where('team_id', $teamId)->where('status', 'open')->latest();
    }
}
