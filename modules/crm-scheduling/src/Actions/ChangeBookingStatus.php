<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Scheduling\Events\BookingChanged;
use Liberu\CRM\Scheduling\Models\Booking;
use Liberu\CRM\Scheduling\Services\SchedulingAudit;
use Liberu\CRM\Scheduling\Services\SchedulingPolicy;

final class ChangeBookingStatus
{
    public function execute(int $teamId, int $actorId, int $bookingId, string $status, array $data = []): Booking
    {
        if (! app(SchedulingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }if (! in_array($status, ['confirmed', 'rescheduled', 'cancelled', 'no_show'], true)) {
            throw ValidationException::withMessages(['status' => 'Invalid booking status.']);
        }$booking = Booking::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($bookingId);
        if ($booking->status === 'cancelled') {
            throw ValidationException::withMessages(['status' => 'Cancelled bookings cannot be changed.']);
        }$booking->status = $status;
        if ($status === 'cancelled') {
            $booking->cancel_reason = $data['reason'] ?? null;
        }$booking->save();
        app(SchedulingAudit::class)->record($teamId, $actorId, 'booking_'.$status, ['booking_id' => $booking->id]);
        BookingChanged::dispatch($booking, $status);

        return $booking;
    }
}
