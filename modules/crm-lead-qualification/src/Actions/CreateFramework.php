<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\LeadQualification\Models\QualificationFramework;

final class CreateFramework
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): QualificationFramework
    {
        if (blank($attributes['name'] ?? null)) {
            throw ValidationException::withMessages(['name' => 'A framework name is required.']);
        }
        foreach (['mql_threshold', 'pql_threshold', 'sql_threshold', 'service_qualified_threshold'] as $threshold) {
            if (isset($attributes[$threshold]) && ((int) $attributes[$threshold] < 0 || (int) $attributes[$threshold] > 100)) {
                throw ValidationException::withMessages([$threshold => 'Thresholds must be between 0 and 100.']);
            }
        }

        $thresholds = [
            (int) ($attributes['mql_threshold'] ?? 50),
            (int) ($attributes['pql_threshold'] ?? 65),
            (int) ($attributes['sql_threshold'] ?? 80),
            (int) ($attributes['service_qualified_threshold'] ?? 90),
        ];
        if ($thresholds[0] > $thresholds[1] || $thresholds[1] > $thresholds[2] || $thresholds[2] > $thresholds[3]) {
            throw ValidationException::withMessages(['thresholds' => 'Qualification thresholds must be ordered from lowest to highest.']);
        }

        return DB::transaction(fn (): QualificationFramework => QualificationFramework::query()->create(array_merge($attributes, ['team_id' => $teamId, 'actor_id' => $actorId]))->refresh());
    }
}
