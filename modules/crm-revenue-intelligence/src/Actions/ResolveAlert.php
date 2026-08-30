<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Actions;

use Liberu\CRM\RevenueIntelligence\Models\RevenueIntelligenceAlert;
use Liberu\CRM\RevenueIntelligence\Services\RevenueIntelligencePolicy;

final class ResolveAlert
{
    public function __construct(private readonly RevenueIntelligencePolicy $policy) {}

    public function execute(int $teamId, int $userId, int $alertId): RevenueIntelligenceAlert
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $alert = RevenueIntelligenceAlert::query()->where('team_id', $teamId)->findOrFail($alertId);
        $alert->update(['status' => 'resolved', 'resolved_at' => now()]);

        return $alert->refresh();
    }
}
