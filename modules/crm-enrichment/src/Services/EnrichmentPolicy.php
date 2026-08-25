<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment\Services;

use App\Models\Team;

final class EnrichmentPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);

        return $team !== null && ((int) $team->user_id === $userId || $team->users()->whereKey($userId)->wherePivotIn('role', ['admin', 'manager', 'marketing', 'sales rep'])->exists());
    }
}
