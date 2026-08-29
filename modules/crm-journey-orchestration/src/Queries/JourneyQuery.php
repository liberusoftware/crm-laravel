<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Queries;

use Liberu\CRM\JourneyOrchestration\Models\Journey;

final class JourneyQuery
{
    public function forTeam(int $teamId)
    {
        return Journey::query()->where('team_id', $teamId)->with('runs')->latest();
    }
}
