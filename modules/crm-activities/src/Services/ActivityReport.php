<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Services;

use Illuminate\Support\Carbon;
use Liberu\CRM\Activities\Models\Activity;

final class ActivityReport
{
    /** @return array<string, int|float> */
    public function summarize(int $teamId, Carbon $from, Carbon $until): array
    {
        $query = Activity::query()->where('team_id', $teamId)->whereBetween('created_at', [$from, $until]);

        return ['total' => (clone $query)->count(), 'completed' => (clone $query)->where('status', 'completed')->count(), 'overdue' => (clone $query)->where('status', 'planned')->whereNotNull('due_at')->where('due_at', '<', now())->count(), 'completion_rate' => round(((int) (clone $query)->count() === 0 ? 0 : ((int) (clone $query)->where('status', 'completed')->count() / (int) (clone $query)->count()) * 100), 2)];
    }
}
