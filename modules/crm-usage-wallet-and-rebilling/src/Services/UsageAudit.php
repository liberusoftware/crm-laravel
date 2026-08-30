<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Services;

use Liberu\CRM\UsageWalletAndRebilling\Models\UsageAudit as Audit;

final class UsageAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): void
    {
        $safe = [];
        foreach ($details as $key => $value) {
            $safe[$key] = preg_match('/token|secret|password|key/i', (string) $key) ? '[REDACTED]' : $value;
        } Audit::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => $safe, 'request_id' => request()->header('X-Request-ID')]);
    }
}
