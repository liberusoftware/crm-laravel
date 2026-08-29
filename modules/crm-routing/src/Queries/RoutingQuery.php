<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Queries;

use Liberu\CRM\Routing\Models\RoutingAgent;
use Liberu\CRM\Routing\Models\RoutingAssignment;
use Liberu\CRM\Routing\Models\RoutingRule;

final class RoutingQuery
{
    public function rules(int $teamId)
    {
        return RoutingRule::query()->where('team_id', $teamId)->orderBy('priority');
    }

    public function agents(int $teamId)
    {
        return RoutingAgent::query()->where('team_id', $teamId)->where('active', true)->orderBy('workload')->orderBy('last_assigned_at');
    }

    public function assignments(int $teamId)
    {
        return RoutingAssignment::query()->where('team_id', $teamId)->latest();
    }
}
