<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Services;

use App\Models\Team;

final class TelephonyPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $team = Team::query()->find($teamId);

        return $team !== null && ((int) $team->user_id === $userId || (bool) $team->users()->whereKey($userId)->wherePivotIn('role', ['admin', 'manager'])->exists());
    }
}
