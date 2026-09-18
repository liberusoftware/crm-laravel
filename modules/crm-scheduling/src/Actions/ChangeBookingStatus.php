<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Scheduling\Events\BookingChanged;
use Liberu\CRM\Scheduling\Models\Booking;
use Liberu\CRM\Scheduling\Models\SchedulingLink;
use Liberu\CRM\Scheduling\Services\SchedulingAudit;
use Liberu\CRM\Scheduling\Services\SchedulingPolicy;

final class ChangeBookingStatus
{
    public function execute(int $teamId, int $actorId, int $bookingId, string $status, array $data = []): Booking
    {
        if (! app(SchedulingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        if (! in_array($status, ['confirmed', 'rescheduled', 'cancelled', 'no_show'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid booking status.']);
        }
        $booking = Booking::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($bookingId);
        if (in_array($booking->status, ['cancelled', 'no_show'], true)) {
            throw ValidationException::withMessages(['status' => 'Cancelled bookings cannot be changed.']);
        }
        if ($status === 'rescheduled') {
            $this->reschedule($booking, $teamId, $data);
        }
        $booking->status = $status;
        if ($status === 'cancelled') {
            $booking->cancel_reason = $data['reason'] ?? null;
        }
        $booking->save();
        app(SchedulingAudit::class)->record($teamId, $actorId, 'booking_'.$status, ['booking_id' => $booking->id]);
        BookingChanged::dispatch($booking, $status);

        return $booking;
    }

    /** @param array<string, mixed> $data */
    private function reschedule(Booking $booking, int $teamId, array $data): void
    {
        validator($data, ['starts_at' => ['required', 'date', 'after:now']])->validate();
        $link = SchedulingLink::query()->where('team_id', $teamId)->whereKey($booking->link_id)->where('active', true)->firstOrFail();
        $start = Carbon::parse($data['starts_at']);
        if ($start->lt(now()->addMinutes($link->minimum_notice_minutes))) {
            throw ValidationException::withMessages(['starts_at' => 'Booking does not meet minimum notice.']);
        }
        $end = $start->copy()->addMinutes($link->duration_minutes);
        $overlap = Booking::query()->where('team_id', $teamId)->whereKeyNot($booking->getKey())->whereIn('status', ['confirmed', 'rescheduled'])->where('starts_at', '<', $end->copy()->addMinutes($link->buffer_after))->where('ends_at', '>', $start->copy()->subMinutes($link->buffer_before))->exists();
        if ($overlap) {
            throw ValidationException::withMessages(['starts_at' => 'The requested slot is unavailable.']);
        }
        $booking->starts_at = $start;
        $booking->ends_at = $end;
    }
}
