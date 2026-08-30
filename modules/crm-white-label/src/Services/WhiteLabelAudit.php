<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Services;

use Liberu\CRM\WhiteLabel\Models\WhiteLabelAudit as Audit;

final class WhiteLabelAudit
{
    /** @param array<string, mixed> $changes */
    public function record(int $teamId, ?int $actorId, string $event, array $changes): void
    {
        Audit::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'changes' => $this->redact($changes), 'request_id' => request()->header('X-Request-ID')]);
    }

    /** @param array<string, mixed> $changes @return array<string, mixed> */
    private function redact(array $changes): array
    {
        foreach (['password', 'secret', 'token', 'api_key'] as $key) {
            if (array_key_exists($key, $changes)) {
                $changes[$key] = '[REDACTED]';
            }
        }

        return $changes;
    }
}
