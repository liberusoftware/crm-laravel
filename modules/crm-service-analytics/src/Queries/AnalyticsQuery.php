<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Queries;

use Liberu\CRM\ServiceAnalytics\Models\AnalyticsSnapshot;

final class AnalyticsQuery
{
    public function snapshots(int $teamId)
    {
        return AnalyticsSnapshot::query()->where('team_id', $teamId)->latest('period_start');
    }

    public function metric(int $teamId, string $metric, $from = null, $to = null)
    {
        $q = $this->snapshots($teamId)->where('metric', $metric);
        if ($from !== null) {
            $q->where('period_start', '>=', $from);
        }if ($to !== null) {
            $q->where('period_end', '<=', $to);
        }

        return $q;
    }

    public function summary(int $teamId, $from = null, $to = null): array
    {
        $metrics = ['volume', 'backlog', 'deflection', 'first_response', 'resolution', 'reopen', 'transfer', 'sla', 'satisfaction', 'quality', 'staffing', 'cost_to_serve'];
        $result = [];
        foreach ($metrics as $metric) {
            $result[$metric] = (float) $this->metric($teamId, $metric, $from, $to)->sum('value');
        }

        return $result;
    }
}
