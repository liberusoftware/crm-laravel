<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinars\Models\EventRegistration;

final class RegisterAttendee
{
    public function execute(int $teamId, CrmEvent $event, array $input): EventRegistration
    {
        abort_unless($event->team_id === $teamId && in_array($event->status, ['published', 'draft'], true), 403);
        $data = Validator::make($input, ['email' => ['required', 'email', 'max:255'], 'attendee_id' => ['nullable', 'integer'], 'ticket' => ['nullable', 'string', 'max:60'], 'metadata' => ['nullable', 'array']])->validate();

        return DB::transaction(function () use ($teamId, $event, $data): EventRegistration {
            $count = EventRegistration::query()->where('event_id', $event->id)->whereIn('status', ['registered', 'checked_in'])->lockForUpdate()->count();
            abort_if($event->capacity !== null && $count >= $event->capacity, 422, 'Event capacity has been reached.');

            return EventRegistration::query()->create(['team_id' => $teamId, 'event_id' => $event->id, 'status' => 'registered', ...$data]);
        });
    }
}
