<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;

final class GrantConsent
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, string $subjectType, int $subjectId, array $attributes, ?int $actorId = null): ConsentRecord
    {
        return DB::transaction(function () use ($teamId, $subjectType, $subjectId, $attributes, $actorId): ConsentRecord {
            $record = ConsentRecord::query()->create(array_merge($attributes, ['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'actor_id' => $actorId, 'status' => 'granted', 'consented_at' => $attributes['consented_at'] ?? now()]));

            return $record->refresh();
        });
    }
}
