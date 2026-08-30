<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Queries;

use Liberu\CRM\UnifiedConversations\Models\Conversation;

final class ConversationQuery
{
    public function list(int $teamId)
    {
        return Conversation::query()->where('team_id', $teamId)->latest()->paginate(25);
    }

    public function find(int $teamId, int $id): Conversation
    {
        return Conversation::query()->where('team_id', $teamId)->findOrFail($id);
    }
}
