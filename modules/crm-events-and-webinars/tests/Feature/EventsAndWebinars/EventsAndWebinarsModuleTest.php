<?php

declare(strict_types=1);

namespace Tests\Feature\EventsAndWebinars;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\EventsAndWebinars\Actions\CheckInAttendee;
use Liberu\CRM\EventsAndWebinars\Actions\CreateEvent;
use Liberu\CRM\EventsAndWebinars\Actions\RegisterAttendee;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class EventsAndWebinarsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_capacity_registration_and_check_in_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $event = app(CreateEvent::class)->execute($team->id, $owner->id, ['name' => 'CRM webinar', 'slug' => 'crm-webinar', 'format' => 'virtual', 'status' => 'published', 'capacity' => 1, 'starts_at' => '2026-09-02 09:00', 'ends_at' => '2026-09-02 10:00']);
        $registration = app(RegisterAttendee::class)->execute($team->id, $event, ['email' => 'attendee@example.com']);
        $checkedIn = app(CheckInAttendee::class)->execute($team->id, $registration);
        $this->assertSame('checked_in', $checkedIn->status);
        $this->assertDatabaseHas('crm_event_registrations', ['event_id' => $event->id, 'status' => 'checked_in']);
        $this->expectException(HttpException::class);
        app(RegisterAttendee::class)->execute($team->id, $event, ['email' => 'second@example.com']);
    }
}
