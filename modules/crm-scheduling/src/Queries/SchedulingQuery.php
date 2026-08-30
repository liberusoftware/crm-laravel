<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Queries;

use Liberu\CRM\Scheduling\Models\Booking;
use Liberu\CRM\Scheduling\Models\SchedulingLink;

final class SchedulingQuery
{
    public function links(int $teamId)
    {
        return SchedulingLink::query()->where('team_id', $teamId)->latest();
    }

    public function bookings(int $teamId)
    {
        return Booking::query()->where('team_id', $teamId)->latest('starts_at');
    }
}
