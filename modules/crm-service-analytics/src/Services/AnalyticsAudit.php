<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Services;

use Illuminate\Support\Str;
use Liberu\CRM\ServiceAnalytics\Models\AnalyticsAudit as Audit;

final class AnalyticsAudit
{
    public function record(int $teamId, ?int $actorId, string $event, array $details = []): Audit
    {
        return Audit::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'details' => $details, 'request_id' => Str::limit((string) request()->header('X-Request-Id'), 255, '') ?: null]);
    }
}
