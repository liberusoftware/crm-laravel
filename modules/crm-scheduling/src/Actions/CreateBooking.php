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

final class CreateBooking
{
    public function execute(int $teamId, int $actorId, array $data): Booking
    {
        if (! app(SchedulingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['link_id' => ['required', 'integer'], 'invitee_name' => ['required', 'string', 'max:255'], 'invitee_email' => ['required', 'email'], 'starts_at' => ['required', 'date', 'after:now'], 'idempotency_key' => ['required', 'string', 'max:255'], 'answers' => ['nullable', 'array']])->validate();
        $existing = Booking::query()->where('team_id', $teamId)->where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing !== null) {
            return $existing;
        }

        $link = SchedulingLink::query()->where('team_id', $teamId)->whereKey($data['link_id'])->where('active', true)->firstOrFail();
        $start = Carbon::parse($data['starts_at']);
        $minimum = now()->addMinutes($link->minimum_notice_minutes);
        if ($start->lt($minimum)) {
            throw ValidationException::withMessages(['starts_at' => 'Booking does not meet minimum notice.']);
        }$end = $start->copy()->addMinutes($link->duration_minutes);
        if (Booking::query()->where('team_id', $teamId)->where('status', 'confirmed')->where('starts_at', '<', $end->copy()->addMinutes($link->buffer_after))->where('ends_at', '>', $start->copy()->subMinutes($link->buffer_before))->exists()) {
            throw ValidationException::withMessages(['starts_at' => 'The requested slot is unavailable.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $data, $start, $end) {
            $existing = Booking::query()->where('team_id', $teamId)->where('idempotency_key', $data['idempotency_key'])->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }$booking = Booking::query()->create(array_merge($data, ['team_id' => $teamId, 'starts_at' => $start, 'ends_at' => $end, 'status' => 'confirmed']));
            app(SchedulingAudit::class)->record($teamId, $actorId, 'booking_created', ['booking_id' => $booking->id]);
            BookingChanged::dispatch($booking, 'created');

            return $booking;
        });
    }
}
