<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Queries;

use Liberu\CRM\OmnichannelService\Models\Conversation;

final class ConversationQuery
{
    public function forTeam(int $teamId)
    {
        return Conversation::query()->where('team_id', $teamId)->with(['interactions', 'workspaceEvents'])->latest();
    }
}
