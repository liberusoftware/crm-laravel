<?php

declare(strict_types=1);

namespace Tests\Feature\SalesEngagement;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\SalesEngagement\Actions\AddStep;
use Liberu\CRM\SalesEngagement\Actions\CreateSequence;
use Liberu\CRM\SalesEngagement\Actions\EnrollContact;
use Liberu\CRM\SalesEngagement\Actions\RecordEngagementEvent;
use Liberu\CRM\SalesEngagement\Actions\StopEnrollment;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Tests\TestCase;

final class SalesEngagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequence_steps_enrollment_reentry_and_stop_rules_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Outbound cadence', 'timezone' => 'UTC', 'stop_rules' => ['reply' => true], 'throttle' => ['per_day' => 25]]);
        app(AddStep::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'position' => 1, 'channel' => 'email', 'template' => 'Hello']);
        $enrollment = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => 10]);
        $reentered = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => 10, 'reentry' => true]);
        app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => 10, 'event' => 'reply']);
        app(StopEnrollment::class)->execute($team->id, $owner->id, $enrollment->id, 'reply');

        self::assertSame($enrollment->id, $reentered->id);
        self::assertSame(1, Enrollment::query()->where('team_id', $team->id)->count());
        self::assertSame('stopped', $enrollment->fresh()->status);
        self::assertSame(1, $enrollment->fresh()->reentry_count);
    }
}
