<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\Activities\Models\Activity;

final class CompleteActivities
{
    /** @param list<int> $activityIds */
    public function execute(int $teamId, array $activityIds, ?string $outcome = null, ?string $notes = null): int
    {
        return DB::transaction(function () use ($teamId, $activityIds, $outcome, $notes): int {
            return Activity::query()->where('team_id', $teamId)->whereIn('id', $activityIds)->whereNotIn('status', ['completed', 'cancelled'])->update(['status' => 'completed', 'outcome' => $outcome, 'outcome_notes' => $notes, 'completed_at' => now(), 'updated_at' => now()]);
        });
    }
}
