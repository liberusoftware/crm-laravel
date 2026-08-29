<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots\Actions;

use Liberu\CRM\ChatAndBots\Models\ChatBot;

final class CreateChatBot
{
    /** @param array<string,mixed> $playbook */
    public function execute(int $teamId, int $ownerId, string $name, array $playbook, array $channels = []): ChatBot
    {
        $name = trim($name);
        abort_unless($name !== '' && $playbook !== [], 422);

        return ChatBot::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'name' => $name, 'status' => 'draft', 'playbook' => $playbook, 'channels' => $channels]);
    }
}
