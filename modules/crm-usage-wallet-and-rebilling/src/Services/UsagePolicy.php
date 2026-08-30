<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Services;

use App\Models\Team;

final class UsagePolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);
        if (! $team) {
            return false;
        }

        return (int) $team->user_id === $userId || $team->users()->whereKey($userId)->wherePivotIn('role', ['admin', 'manager'])->exists();
    }
}
