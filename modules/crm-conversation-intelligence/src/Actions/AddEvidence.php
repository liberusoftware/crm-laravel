<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Actions;

use Liberu\CRM\ConversationIntelligence\Models\Conversation;
use Liberu\CRM\ConversationIntelligence\Models\ConversationEvidence;

final class AddEvidence
{
    public function execute(int $teamId, Conversation $conversation, string $kind, string $label, string $content, array $metadata = []): ConversationEvidence
    {
        abort_unless((int) $conversation->team_id === $teamId && in_array($kind, ['topic', 'question', 'objection', 'competitor', 'action_item', 'highlight'], true) && $label !== '' && $content !== '', 422);

        return ConversationEvidence::query()->create(['team_id' => $teamId, 'conversation_id' => $conversation->id, 'kind' => $kind, 'label' => $label, 'content' => $content, 'metadata' => $metadata]);
    }
}
