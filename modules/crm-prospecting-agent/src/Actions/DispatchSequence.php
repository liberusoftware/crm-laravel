<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Liberu\CRM\ProspectingAgent\Models\AgentSequence;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class DispatchSequence
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $sequenceId): AgentSequence
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $sequence = AgentSequence::query()->where('team_id', $teamId)->where('status', 'prepared')->findOrFail($sequenceId);
        $sequence->update(['status' => 'dispatched', 'dispatched_at' => now()]);

        return $sequence->refresh();
    }
}
