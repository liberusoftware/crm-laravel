<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Actions;

use Liberu\CRM\ChatAndBots\Models\ChatBot;
use Liberu\CRM\ChatAndBots\Models\ChatSession;

final class StartChatSession
{
    public function execute(int $teamId, ChatBot $bot, string $visitorKey, string $channel = 'web'): ChatSession
    {
        abort_unless((int) $bot->team_id === $teamId && $bot->status === 'active' && $visitorKey !== '' && in_array($channel, ['web', 'sms', 'whatsapp', 'messenger'], true), 422);

        return ChatSession::query()->create(['team_id' => $teamId, 'bot_id' => $bot->id, 'visitor_key' => $visitorKey, 'channel' => $channel, 'status' => 'active', 'transcript' => []]);
    }
}
