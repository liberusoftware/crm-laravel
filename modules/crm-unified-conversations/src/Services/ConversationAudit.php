<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Services;

use Liberu\CRM\UnifiedConversations\Models\ConversationAudit as ConversationAuditModel;

final class ConversationAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): void
    {
        ConversationAuditModel::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => $details]);
    }
}
