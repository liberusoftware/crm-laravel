<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Queries;

use Liberu\CRM\ResourcePlanning\Models\ResourceBooking;
use Liberu\CRM\ResourcePlanning\Models\ResourceCapacity;
use Liberu\CRM\ResourcePlanning\Models\ResourceForecast;
use Liberu\CRM\ResourcePlanning\Models\ResourceSkill;

final class ResourcePlanningQuery
{
    public function skills(int $teamId)
    {
        return ResourceSkill::query()->where('team_id', $teamId)->latest();
    }

    public function capacity(int $teamId)
    {
        return ResourceCapacity::query()->where('team_id', $teamId)->latest();
    }

    public function bookings(int $teamId)
    {
        return ResourceBooking::query()->where('team_id', $teamId)->latest();
    }

    public function forecasts(int $teamId)
    {
        return ResourceForecast::query()->where('team_id', $teamId)->latest();
    }
}
