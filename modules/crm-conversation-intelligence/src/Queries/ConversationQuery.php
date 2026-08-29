<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Liberu\CRM\ConversationIntelligence\Models\Conversation;
use Liberu\CRM\ConversationIntelligence\Models\ConversationEvidence;

final class ConversationQuery
{
    public function conversations(int $teamId): Builder
    {
        return Conversation::query()->where('team_id', $teamId)->latest();
    }

    public function evidence(int $teamId, string $search): Collection
    {
        return ConversationEvidence::query()->where('team_id', $teamId)->where(fn ($q) => $q->where('label', 'like', '%'.$search.'%')->orWhere('content', 'like', '%'.$search.'%'))->get();
    }
}
