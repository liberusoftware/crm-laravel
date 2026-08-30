<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\CRM\ConsentAndPreferences\Models\PreferenceRecord;

final class SetPreference
{
    /** @param array<string, mixed> $values */
    public function execute(int $teamId, string $subjectType, int $subjectId, string $channel, string $topic, array $values, ?int $actorId = null): PreferenceRecord
    {
        return DB::transaction(function () use ($teamId, $subjectType, $subjectId, $channel, $topic, $values, $actorId): PreferenceRecord {
            return PreferenceRecord::query()->updateOrCreate(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'channel' => $channel, 'topic' => $topic], array_merge($values, ['actor_id' => $actorId]))->refresh();
        });
    }
}
