<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Activities\Models\Activity;

final class CreateActivity
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): Activity
    {
        if (($attributes['kind'] ?? null) === 'meeting' && empty($attributes['starts_at'])) {
            throw ValidationException::withMessages(['starts_at' => 'Meetings require a start time.']);
        }
        if (isset($attributes['recurrence']) && ! in_array($attributes['recurrence'], ['daily', 'weekly', 'monthly'], true)) {
            throw ValidationException::withMessages(['recurrence' => 'Unsupported recurrence interval.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $attributes): Activity {
            return Activity::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId, 'status' => $attributes['status'] ?? 'planned']))->refresh();
        });
    }
}
