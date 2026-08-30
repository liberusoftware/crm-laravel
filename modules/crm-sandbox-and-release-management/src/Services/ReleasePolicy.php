<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Services;

use App\Models\Team;

final class ReleasePolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);

        return $team !== null && ((int) $team->user_id === $userId || $team->users()->whereKey($userId)->wherePivotIn('role', ['admin', 'manager'])->exists());
    }
}
