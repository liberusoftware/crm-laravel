<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChatAndBots\Models\ChatBot;
use Liberu\CRM\ChatAndBots\Models\ChatSession;

final class ChatBotQuery
{
    public function bots(int $teamId): Builder
    {
        return ChatBot::query()->where('team_id', $teamId)->latest();
    }

    public function sessions(int $teamId): Builder
    {
        return ChatSession::query()->where('team_id', $teamId)->latest();
    }
}
