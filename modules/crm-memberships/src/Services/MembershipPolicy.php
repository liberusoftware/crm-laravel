<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Services;

use App\Models\Team;

final class MembershipPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);

        return $team !== null && ($team->user_id === $userId || $team->users()->whereKey($userId)->wherePivotIn('role', ['admin', 'owner', 'manager', 'sales', 'support'])->exists());
    }
}
