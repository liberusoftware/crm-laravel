<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Services;

use Liberu\CRM\Scheduling\Models\SchedulingAudit as Audit;

final class SchedulingAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): Audit
    {
        return Audit::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => $details]);
    }
}
