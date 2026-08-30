<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Actions;

use Liberu\CRM\ConversationIntelligence\Models\Conversation;

final class AnalyzeConversation
{
    public function execute(int $teamId, Conversation $conversation, string $transcript, array $insights = []): Conversation
    {
        abort_unless((int) $conversation->team_id === $teamId && trim($transcript) !== '', 422);
        $conversation->update(['status' => 'analyzed', 'transcript' => $transcript, 'summary' => ['word_count' => str_word_count($transcript), 'generated_at' => now()->toIso8601String()], 'insights' => $insights]);

        return $conversation->refresh();
    }
}
