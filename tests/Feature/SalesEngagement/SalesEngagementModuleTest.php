<?php

declare(strict_types=1);

namespace Tests\Feature\SalesEngagement;

use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Actions\AddStep;
use Liberu\CRM\SalesEngagement\Actions\CreateSequence;
use Liberu\CRM\SalesEngagement\Actions\EnrollContact;
use Liberu\CRM\SalesEngagement\Actions\RecordEngagementEvent;
use Liberu\CRM\SalesEngagement\Actions\StopEnrollment;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Tests\TestCase;

final class SalesEngagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequence_resource_exposes_the_complete_filament_lifecycle(): void
    {
        self::assertSame(['index', 'create', 'edit'], array_keys(SequenceResource::getPages()));
    }

    public function test_sequence_steps_enrollment_reentry_and_stop_rules_are_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $contact = Contact::factory()->create(['team_id' => $team->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Outbound cadence', 'timezone' => 'UTC', 'stop_rules' => ['reply' => true], 'throttle' => ['per_day' => 25]]);
        app(AddStep::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'position' => 1, 'channel' => 'email', 'template' => 'Hello']);
        $enrollment = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $contact->id]);
        $reentered = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $contact->id, 'reentry' => true]);
        app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => $contact->id, 'event' => 'reply']);
        app(StopEnrollment::class)->execute($team->id, $owner->id, $enrollment->id, 'reply');

        self::assertSame($enrollment->id, $reentered->id);
        self::assertSame(1, Enrollment::query()->where('team_id', $team->id)->count());
        self::assertSame('stopped', $enrollment->fresh()->status);
        self::assertSame(1, $enrollment->fresh()->reentry_count);
    }

    public function test_contacts_from_another_team_cannot_be_enrolled_or_recorded(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => $owner->id]);
        $foreignContact = Contact::factory()->create(['team_id' => $otherTeam->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Outbound cadence', 'timezone' => 'UTC']);

        try {
            app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $foreignContact->id]);
            self::fail('A foreign contact must not be enrolled.');
        } catch (ValidationException $exception) {
            self::assertSame('Contact does not belong to this team.', $exception->errors()['contact_id'][0]);
        }

        $this->expectException(ValidationException::class);
        app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => $foreignContact->id, 'event' => 'reply']);
    }
}
