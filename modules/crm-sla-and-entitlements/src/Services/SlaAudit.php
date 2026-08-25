<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Services;

use Illuminate\Support\Str;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCaseEvent;

final class SlaAudit
{
    public function record(int $teamId, ?int $actorId, int $caseId, string $type, array $payload = []): SlaCaseEvent
    {
        return SlaCaseEvent::query()->create(['team_id' => $teamId, 'case_id' => $caseId, 'actor_id' => $actorId, 'type' => $type, 'payload' => $payload, 'occurred_at' => now(), 'request_id' => Str::limit((string) request()->header('X-Request-Id'), 255, '') ?: null]);
    }
}
