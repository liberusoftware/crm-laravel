<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Queries;

use Liberu\CRM\ProspectingAgent\Models\AgentEngagement;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;
use Liberu\CRM\ProspectingAgent\Models\AgentSequence;
use Liberu\CRM\ProspectingAgent\Models\AgentTarget;

final class ProspectingAgentQuery
{
    public function runs(int $teamId)
    {
        return AgentRun::query()->where('team_id', $teamId)->latest();
    }

    public function targets(int $teamId)
    {
        return AgentTarget::query()->where('team_id', $teamId)->latest();
    }

    public function sequences(int $teamId)
    {
        return AgentSequence::query()->where('team_id', $teamId)->latest();
    }

    public function engagements(int $teamId)
    {
        return AgentEngagement::query()->where('team_id', $teamId)->latest();
    }
}
