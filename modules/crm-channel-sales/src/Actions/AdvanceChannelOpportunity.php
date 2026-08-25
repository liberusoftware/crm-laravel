<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales\Actions;

use Liberu\CRM\ChannelSales\Models\ChannelEvent;
use Liberu\CRM\ChannelSales\Models\ChannelOpportunity;

final class AdvanceChannelOpportunity
{
    public function execute(int $teamId, int $actorId, ChannelOpportunity $opportunity, string $stage, string $handoffStatus = 'pending'): ChannelEvent
    {
        abort_unless((int) $opportunity->team_id === $teamId && in_array($stage, ['registered', 'qualified', 'quoted', 'won', 'lost'], true) && in_array($handoffStatus, ['pending', 'ready', 'handed_off'], true), 422);
        $opportunity->update(['stage' => $stage, 'handoff_status' => $handoffStatus, 'forecast' => ['amount' => $opportunity->amount, 'probability' => ['registered' => .1, 'qualified' => .3, 'quoted' => .6, 'won' => 1, 'lost' => 0][$stage]]]);
        $commission = $stage === 'won' ? (float) $opportunity->amount * ((float) $opportunity->commission_rate / 100) : null;

        return ChannelEvent::query()->create(['team_id' => $teamId, 'opportunity_id' => $opportunity->id, 'actor_id' => $actorId, 'type' => 'stage_changed', 'commission' => $commission, 'payload' => ['stage' => $stage, 'handoff_status' => $handoffStatus]]);
    }
}
