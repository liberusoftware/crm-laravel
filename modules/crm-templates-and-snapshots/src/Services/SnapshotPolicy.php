<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Services;

use Illuminate\Support\Facades\DB;

final class SnapshotPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        return (int) DB::table('teams')->where('id', $teamId)->value('user_id') === $userId
            || DB::table('team_user')->where('team_id', $teamId)->where('user_id', $userId)->whereIn('role', ['admin', 'manager'])->where(function ($query): void {
                $query->whereNull('status')->orWhere('status', 'active');
            })->exists();
    }
}
