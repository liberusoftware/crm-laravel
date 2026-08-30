<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Queries;

use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinars\Models\EventRegistration;
use Liberu\CRM\EventsAndWebinars\Models\EventSession;

final class EventQuery
{
    public function events(int $teamId)
    {
        return CrmEvent::query()->where('team_id', $teamId)->orderBy('starts_at');
    }

    public function sessions(int $teamId, int $eventId)
    {
        return EventSession::query()->where('team_id', $teamId)->where('event_id', $eventId)->orderBy('starts_at');
    }

    public function attendance(int $teamId, int $eventId): array
    {
        $query = EventRegistration::query()->where('team_id', $teamId)->where('event_id', $eventId);

        return ['registered' => (clone $query)->where('status', 'registered')->count(), 'checked_in' => (clone $query)->where('status', 'checked_in')->count()];
    }
}
