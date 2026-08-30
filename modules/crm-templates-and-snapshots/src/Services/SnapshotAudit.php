<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Services;

use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotAudit as Audit;

final class SnapshotAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): void
    {
        Audit::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => $details]);
    }
}
