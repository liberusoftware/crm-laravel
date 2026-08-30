<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Actions;

use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;

final class StartReceptionConversation
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, ReceptionAgent $agent, array $input = []): ReceptionConversation
    {
        abort_unless((int) $agent->team_id === $teamId, 404);
        abort_unless($agent->status === 'active', 422);

        return ReceptionConversation::query()->create(['team_id' => $teamId, 'agent_id' => $agent->getKey(), 'external_key' => $input['external_key'] ?? null, 'status' => 'active', 'transcript' => [], 'qualification' => [], 'booking' => []]);
    }
}
