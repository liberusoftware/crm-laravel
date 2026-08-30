<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\ConsentAndPreferences\Models\SuppressionRecord;

final class SuppressSubject
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, string $subjectType, int $subjectId, array $attributes, ?int $actorId = null): SuppressionRecord
    {
        return DB::transaction(function () use ($teamId, $subjectType, $subjectId, $attributes, $actorId): SuppressionRecord {
            return SuppressionRecord::query()->create(array_merge($attributes, ['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'actor_id' => $actorId]))->refresh();
        });
    }
}
