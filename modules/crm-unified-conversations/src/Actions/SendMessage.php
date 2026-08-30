<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\UnifiedConversations\Models\Conversation;
use Liberu\CRM\UnifiedConversations\Models\ConversationMessage;
use Liberu\CRM\UnifiedConversations\Services\ConversationPolicy;

final class SendMessage
{
    public function execute(int $teamId, int $actorId, int $conversationId, array $data): ConversationMessage
    {
        if (! app(ConversationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['body' => ['required', 'string', 'max:10000'], 'internal' => ['boolean'], 'idempotency_key' => ['nullable', 'string', 'max:255']])->validate();
        Conversation::query()->where('team_id', $teamId)->findOrFail($conversationId);

        return ConversationMessage::query()->firstOrCreate(['team_id' => $teamId, 'conversation_id' => $conversationId, 'idempotency_key' => $data['idempotency_key'] ?? null], ['sender_id' => $actorId, 'body' => $data['body'], 'internal' => $data['internal'] ?? false, 'delivery_status' => 'sent']);
    }
}
