<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\UnifiedConversations\Models\Conversation;
use Liberu\CRM\UnifiedConversations\Services\ConversationPolicy;

final class AssignConversation
{
    public function execute(int $teamId, int $actorId, int $conversationId, ?int $assignee): Conversation
    {
        if (! app(ConversationPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        if ($assignee !== null && ! DB::table('team_user')->where('team_id', $teamId)->where('user_id', $assignee)->exists() && (int) DB::table('teams')->where('id', $teamId)->value('user_id') !== $assignee) {
            throw ValidationException::withMessages(['assignee' => 'The assignee is not a member of this team.']);
        }
        $c = Conversation::query()->where('team_id', $teamId)->findOrFail($conversationId);
        $c->setAttribute('assigned_to', $assignee);
        $c->save();

        return $c->fresh();
    }
}
