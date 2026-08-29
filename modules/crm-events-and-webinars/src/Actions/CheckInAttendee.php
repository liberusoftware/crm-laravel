<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Actions;

use Liberu\CRM\EventsAndWebinars\Models\EventRegistration;

final class CheckInAttendee
{
    public function execute(int $teamId, EventRegistration $registration): EventRegistration
    {
        abort_unless($registration->team_id === $teamId && $registration->status === 'registered', 422);
        $registration->update(['status' => 'checked_in', 'checked_in_at' => now()]);

        return $registration->refresh();
    }
}
