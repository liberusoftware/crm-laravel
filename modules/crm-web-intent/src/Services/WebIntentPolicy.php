<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Services;

use Illuminate\Support\Facades\DB;

final class WebIntentPolicy
{
    public function canManage(int $teamId, int $actorId): bool
    {
        if ((int) DB::table('teams')->where('id', $teamId)->value('user_id') === $actorId) {
            return true;
        }

        return DB::table('team_user')->where('team_id', $teamId)->where('user_id', $actorId)->whereIn('role', ['owner', 'admin', 'manager'])->exists();
    }
}
