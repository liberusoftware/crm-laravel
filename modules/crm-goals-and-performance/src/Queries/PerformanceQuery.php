<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Queries;

use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;

final class PerformanceQuery
{
    public function forTeam(int $teamId)
    {
        return PerformanceGoal::query()->where('team_id', $teamId)->with('events')->orderByDesc('actual');
    }
}
