<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Services;

use Illuminate\Support\Facades\DB;

final class SlaPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        $owner = DB::table('teams')->where('id', $teamId)->value('user_id');

        return $owner !== null && ((int) $owner === $userId || DB::table('team_user')
            ->where('team_id', $teamId)
            ->where('user_id', $userId)
            ->whereIn('role', ['admin', 'manager'])
            ->where(function ($query): void {
                $query->whereNull('status')->orWhere('status', 'active');
            })
            ->exists());
    }
}
