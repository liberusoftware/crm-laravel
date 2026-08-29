<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Services;

use Illuminate\Support\Facades\DB;

final class ConversationPolicy
{
    public function canManage(int $teamId, int $userId): bool
    {
        return (int) DB::table('teams')->where('id', $teamId)->value('user_id') === $userId
            || DB::table('team_user')->where('team_id', $teamId)->where('user_id', $userId)->whereIn('role', ['owner', 'admin', 'manager', 'sales rep'])->exists();
    }
}
