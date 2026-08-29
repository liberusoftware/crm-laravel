<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent\Queries;

use Liberu\CRM\MarketingAgent\Models\AgentRequest;

final class AgentQuery
{
    public function forTeam(int $teamId)
    {
        return AgentRequest::query()->where('team_id', $teamId)->with('checks')->latest();
    }
}
