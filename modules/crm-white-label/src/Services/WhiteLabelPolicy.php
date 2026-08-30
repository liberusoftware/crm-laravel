<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Services;

use Illuminate\Support\Facades\DB;

final class WhiteLabelPolicy
{
    public function canManage(int $teamId, int $actorId): bool
    {
        $owner = DB::table('teams')->where('id', $teamId)->value('user_id');

        if ((int) $owner === $actorId) {
            return true;
        }

        return DB::table('team_user')->where('team_id', $teamId)->where('user_id', $actorId)->whereIn('role', ['owner', 'admin'])->exists();
    }
}
