<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Services;

use Illuminate\Support\Facades\Log;

final class ReleaseAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): void
    {
        Log::channel(config('logging.default'))->info('crm.release.'.$event, ['team_id' => $teamId, 'actor_id' => $actorId, 'details' => $details]);
    }
}
