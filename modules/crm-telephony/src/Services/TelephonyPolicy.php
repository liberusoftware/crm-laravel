<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Services;

use Illuminate\Support\Facades\DB;

final class TelephonyPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        return $this->isTeamMember($teamId, $userId, ['admin', 'manager'], true);
    }

    public function isTeamMember(int $teamId, int $userId, array $roles = [], bool $includeOwner = false): bool
    {
        if ($includeOwner && (int) DB::table('teams')->where('id', $teamId)->value('user_id') === $userId) {
            return true;
        }

        $query = DB::table('team_user')->where('team_id', $teamId)->where('user_id', $userId);
        if ($roles !== []) {
            $query->whereIn('role', $roles);
        }

        return $query->where(function ($query): void {
            $query->whereNull('status')->orWhere('status', 'active');
        })->exists();
    }
}
