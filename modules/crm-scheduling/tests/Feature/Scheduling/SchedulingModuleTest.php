<?php

declare(strict_types=1);

namespace Tests\Feature\Scheduling;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Scheduling\Actions\ChangeBookingStatus;
use Liberu\CRM\Scheduling\Actions\CreateBooking;
use Liberu\CRM\Scheduling\Actions\CreateSchedulingLink;
use Liberu\CRM\Scheduling\Models\Booking;
use Tests\TestCase;

final class SchedulingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_enforces_notice_conflicts_is_idempotent_and_supports_no_show(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $link = app(CreateSchedulingLink::class)->execute($team->id, $owner->id, ['slug' => 'demo', 'name' => 'Demo', 'kind' => 'round_robin', 'duration_minutes' => 30, 'buffer_after' => 10, 'minimum_notice_minutes' => 0, 'questions' => ['company']]);
        $data = ['link_id' => $link->id, 'invitee_name' => 'Ada Lovelace', 'invitee_email' => 'ada@example.test', 'starts_at' => now()->addHours(2), 'idempotency_key' => 'booking-1', 'answers' => ['company' => 'Analytical Engines']];
        $booking = app(CreateBooking::class)->execute($team->id, $owner->id, $data);
        $same = app(CreateBooking::class)->execute($team->id, $owner->id, $data);
        self::assertSame($booking->id, $same->id);
        self::assertSame(1, Booking::query()->where('team_id', $team->id)->count());

        try {
            app(CreateBooking::class)->execute($team->id, $owner->id, array_merge($data, ['idempotency_key' => 'booking-2']));
            self::fail('Overlapping booking was accepted.');
        } catch (ValidationException) {
            self::assertTrue(true);
        }

        app(ChangeBookingStatus::class)->execute($team->id, $owner->id, $booking->id, 'no_show');
        self::assertSame('no_show', $booking->fresh()->status);
    }
}
