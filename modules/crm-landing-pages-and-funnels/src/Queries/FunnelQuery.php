<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels\Queries;

use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;

final class FunnelQuery
{
    public function forTeam(int $teamId)
    {
        return Funnel::query()->where('team_id', $teamId)->with('pages')->latest();
    }
}
