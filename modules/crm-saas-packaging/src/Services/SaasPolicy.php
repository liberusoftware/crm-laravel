<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class SaasPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        return DB::table('teams')
            ->where('teams.id', $teamId)
            ->where(function (Builder $query) use ($userId): void {
                $query
                    ->where('teams.user_id', $userId)
                    ->orWhereExists(function (Builder $membership) use ($userId): void {
                        $membership
                            ->selectRaw('1')
                            ->from('team_user')
                            ->whereColumn('team_user.team_id', 'teams.id')
                            ->where('team_user.user_id', $userId)
                            ->whereIn('team_user.role', ['admin', 'manager']);
                    });
            })
            ->exists();
    }
}
