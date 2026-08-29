<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter\Actions;

use Liberu\CRM\ContactCenter\Models\ContactCenterAgent;

final class SetAgentPresence
{
    public function execute(int $teamId, int $userId, string $presence, int $capacity = 1, array $skills = []): ContactCenterAgent
    {
        abort_unless(in_array($presence, ['offline', 'available', 'busy', 'away', 'wrap_up'], true) && $capacity >= 0, 422);

        return ContactCenterAgent::query()->updateOrCreate(['team_id' => $teamId, 'user_id' => $userId], ['presence' => $presence, 'capacity' => $capacity, 'skills' => $skills]);
    }
}
