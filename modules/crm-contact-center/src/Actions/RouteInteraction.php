<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter\Actions;

use Liberu\CRM\ContactCenter\Models\ContactCenterAgent;
use Liberu\CRM\ContactCenter\Models\ContactCenterEvent;

final class RouteInteraction
{
    public function execute(int $teamId, string $queueKey, string $requiredSkill, int $slaSeconds = 300): ContactCenterEvent
    {
        $agent = ContactCenterAgent::query()->where('team_id', $teamId)->where('presence', 'available')->where('capacity', '>', 0)->get()->first(fn (ContactCenterAgent $a): bool => in_array($requiredSkill, $a->skills ?? [], true));
        abort_unless($agent !== null, 409);
        $agent->decrement('capacity');

        return ContactCenterEvent::query()->create(['team_id' => $teamId, 'agent_id' => $agent->id, 'type' => 'routed', 'queue_key' => $queueKey, 'sla_seconds' => $slaSeconds, 'status' => 'open']);
    }
}
