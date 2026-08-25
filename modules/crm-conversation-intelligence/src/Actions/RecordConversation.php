<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Actions;

use Liberu\CRM\ConversationIntelligence\Models\Conversation;

final class RecordConversation
{
    /** @param array{subject?:string,type?:string,recording_url?:string} $input */
    public function execute(int $teamId, int $userId, array $input): Conversation
    {
        $subject = trim((string) ($input['subject'] ?? ''));
        $type = (string) ($input['type'] ?? 'meeting');
        abort_unless($subject !== '' && in_array($type, ['call', 'meeting'], true), 422);

        return Conversation::query()->create(['team_id' => $teamId, 'owner_id' => $userId, 'subject' => $subject, 'type' => $type, 'status' => 'recorded', 'recording_url' => $input['recording_url'] ?? null]);
    }
}
