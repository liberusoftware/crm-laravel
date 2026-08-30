<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Actions;

use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;

final class ActivateReceptionAgent
{
    public function execute(int $teamId, ReceptionAgent $agent): ReceptionAgent
    {
        abort_unless((int) $agent->team_id === $teamId, 404);
        abort_unless((array) $agent->knowledge !== [] || (array) $agent->tools !== [], 422);
        $agent->update(['status' => 'active']);

        return $agent->fresh();
    }
}
