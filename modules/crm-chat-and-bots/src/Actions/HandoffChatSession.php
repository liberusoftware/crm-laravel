<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Actions;

use Liberu\CRM\ChatAndBots\Models\ChatSession;

final class HandoffChatSession
{
    public function execute(int $teamId, ChatSession $session, string $assignee): ChatSession
    {
        abort_unless((int) $session->team_id === $teamId && $session->status === 'active' && $assignee !== '', 422);
        $session->update(['status' => 'handoff', 'handoff_to' => $assignee]);

        return $session->refresh();
    }
}
