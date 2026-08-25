<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Actions;

use Liberu\CRM\ProspectingAgent\Events\AgentRunApproved;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Liberu\CRM\ProspectingAgent\Services\ProspectingAgentPolicy;

final class ApproveAgentRun
{
    public function __construct(private readonly ProspectingAgentPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $runId): AgentRun
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $run = AgentRun::query()->where('team_id', $teamId)->where('status', 'draft')->findOrFail($runId);
        $run->update(['approved' => true, 'status' => 'approved']);
        event(new AgentRunApproved($run));

        return $run->refresh();
    }
}
