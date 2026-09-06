<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Liberu\CRM\UnifiedConversations\Models\Conversation;
use Liberu\CRM\UnifiedConversations\Models\ConversationMessage;
use Liberu\CRM\UnifiedConversations\Models\ConversationParticipant;

final class SyncExternalConversation
{
    /** @param array<string, mixed> $data */
    public function execute(int $teamId, array $data): Conversation
    {
        $externalId = trim((string) ($data['external_id'] ?? ''));
        $channel = trim((string) ($data['channel'] ?? ''));
        abort_unless($externalId !== '' && $channel !== '', 422);

        return DB::transaction(function () use ($teamId, $data, $externalId, $channel): Conversation {
            $conversation = Conversation::query()->firstOrNew(['team_id' => $teamId, 'channel' => $channel, 'external_id' => $externalId]);
            $conversation->fill([
                'subject' => $data['subject'] ?? $conversation->subject,
                'status' => $data['status'] ?? $conversation->status ?? 'open',
                'priority' => $data['priority'] ?? $conversation->priority ?? 'normal',
                'last_message_at' => $data['last_message_at'] ?? now(),
                'metadata' => $data['metadata'] ?? [],
            ])->save();

            $participant = $data['participant'] ?? [];
            if (is_array($participant) && filled($participant['identity'] ?? null)) {
                ConversationParticipant::query()->updateOrCreate(['conversation_id' => $conversation->id, 'identity' => (string) $participant['identity']], [
                    'team_id' => $teamId,
                    'name' => $participant['name'] ?? null,
                    'role' => $participant['role'] ?? 'customer',
                ]);
            }

            $message = $data['message'] ?? null;
            if (is_array($message) && filled($message['external_id'] ?? null)) {
                ConversationMessage::query()->updateOrCreate(['team_id' => $teamId, 'conversation_id' => $conversation->id, 'external_id' => (string) $message['external_id']], [
                    'body' => (string) ($message['body'] ?? ''),
                    'direction' => $message['direction'] ?? 'inbound',
                    'delivery_status' => $message['delivery_status'] ?? 'received',
                    'metadata' => Arr::except($message, ['body']),
                ]);
            }

            return $conversation->fresh();
        });
    }
}
