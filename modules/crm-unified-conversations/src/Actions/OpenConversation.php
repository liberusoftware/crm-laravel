<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\UnifiedConversations\Models\Conversation;
use Liberu\CRM\UnifiedConversations\Services\ConversationAudit;
use Liberu\CRM\UnifiedConversations\Services\ConversationPolicy;

final class OpenConversation
{
    public function execute(int $teamId, int $actorId, array $data): Conversation
    {
        if (! app(ConversationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['channel' => ['required', 'string', 'max:50'], 'external_id' => ['nullable', 'string', 'max:255'], 'subject' => ['nullable', 'string', 'max:255']])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data) {
            $c = Conversation::query()->firstOrCreate(['team_id' => $teamId, 'channel' => $data['channel'], 'external_id' => $data['external_id'] ?? null], ['subject' => $data['subject'] ?? null, 'status' => 'open']);
            app(ConversationAudit::class)->record($teamId, $actorId, 'conversation_opened', ['conversation_id' => $c->id]);

            return $c;
        });
    }
}
