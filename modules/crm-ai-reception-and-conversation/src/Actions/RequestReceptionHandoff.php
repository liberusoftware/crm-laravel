<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Actions;

use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;

final class RequestReceptionHandoff
{
    public function execute(int $teamId, int $actorId, ReceptionConversation $conversation, string $reason): ReceptionConversation
    {
        abort_unless((int) $conversation->team_id === $teamId, 404);
        abort_unless($conversation->status === 'active' && trim($reason) !== '', 422);
        $conversation->update(['handoff_status' => 'requested', 'status' => 'handoff']);
        $conversation->audits()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => 'handoff_requested', 'payload' => ['reason' => trim($reason)]]);

        return $conversation->fresh();
    }
}
