<?php

declare(strict_types=1);

namespace Tests\Feature\SalesEngagement;

use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Actions\AddStep;
use Liberu\CRM\SalesEngagement\Actions\CreateSequence;
use Liberu\CRM\SalesEngagement\Actions\EnrollContact;
use Liberu\CRM\SalesEngagement\Actions\RecordEngagementEvent;
use Liberu\CRM\SalesEngagement\Actions\StopEnrollment;
use Liberu\CRM\SalesEngagement\Actions\UpdateSequence;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages\EditSequence;
use Liberu\CRM\SalesEngagement\Models\EngagementTask;
use Liberu\CRM\SalesEngagement\Models\Enrollment;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
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

        self::assertSame($enrollment->id, $reentered->id);
        self::assertSame(1, Enrollment::query()->where('team_id', $team->id)->count());
        self::assertSame('stopped', $enrollment->fresh()->status);
        self::assertSame(1, $enrollment->fresh()->reentry_count);
    }

    public function test_sequence_editor_defaults_legacy_rules_and_persists_disabled_triggers(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $owner->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($team);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Editor rules', 'timezone' => 'UTC']);

        Livewire::test(EditSequence::class, ['record' => $sequence->id])
            ->assertFormSet(['stop_rules.reply' => true, 'stop_rules.meeting' => true])
            ->fillForm(['stop_rules.reply' => false, 'stop_rules.meeting' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        self::assertSame(['reply' => false, 'meeting' => false], $sequence->fresh()->stop_rules);
    }

    public static function stopEvents(): array
    {
        return [['reply'], ['meeting']];
    }

    #[DataProvider('stopEvents')]
    public function test_recorded_events_stop_active_enrollments_and_cancel_only_queued_followups(string $event): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $contact = Contact::factory()->create(['team_id' => $team->id]);
        $otherContact = Contact::factory()->create(['team_id' => $team->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Default rules', 'timezone' => 'UTC']);
        $enrollment = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $contact->id]);
        $unaffected = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $otherContact->id]);
        $enrollment->update(['next_run_at' => now()->addDay()]);
        $queued = EngagementTask::query()->create(['team_id' => $team->id, 'enrollment_id' => $enrollment->id, 'channel' => 'email']);
        $completed = EngagementTask::query()->create(['team_id' => $team->id, 'enrollment_id' => $enrollment->id, 'channel' => 'call', 'status' => 'completed', 'completed_at' => now()]);

        $recorded = app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => $contact->id, 'event' => $event, 'payload' => ['source' => 'test']]);

        self::assertSame($event, $recorded->event);
        self::assertSame(['source' => 'test'], $recorded->payload);
        self::assertSame('stopped', $enrollment->fresh()->status);
        self::assertNull($enrollment->fresh()->next_run_at);
        self::assertSame('cancelled', $queued->fresh()->status);
        self::assertSame('completed', $completed->fresh()->status);
        self::assertNotNull($completed->fresh()->completed_at);
        self::assertSame('active', $unaffected->fresh()->status);
    }

    public function test_disabled_rules_and_non_stop_events_leave_enrollments_active(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $contact = Contact::factory()->create(['team_id' => $team->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Optional rules', 'timezone' => 'UTC', 'stop_rules' => ['reply' => false, 'meeting' => false]]);
        $enrollment = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $contact->id]);

        foreach (['reply', 'meeting', 'email_open', 'email_click', 'call_completed'] as $event) {
            app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => $contact->id, 'event' => $event]);
            self::assertSame('active', $enrollment->fresh()->status);
        }

        app(UpdateSequence::class)->execute($team->id, $owner->id, $sequence->id, ['name' => $sequence->name, 'timezone' => 'UTC', 'status' => 'active', 'stop_rules' => ['reply' => true, 'meeting' => false]]);
        app(RecordEngagementEvent::class)->execute($team->id, $owner->id, ['contact_id' => $contact->id, 'event' => 'reply']);
        self::assertSame('stopped', $enrollment->fresh()->status);
    }

    public function test_event_endpoint_applies_each_sequence_rule_without_changing_terminal_or_foreign_enrollments(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => $owner->id]);
        $owner->forceFill(['current_team_id' => $team->id])->save();
        $contact = Contact::factory()->create(['team_id' => $team->id]);
        $enrollments = [];

        foreach (['enabled', 'disabled', 'completed', 'foreign'] as $name) {
            $sequenceTeam = $name === 'foreign' ? $otherTeam : $team;
            $sequence = app(CreateSequence::class)->execute($sequenceTeam->id, $owner->id, ['name' => $name, 'timezone' => 'UTC', 'stop_rules' => ['reply' => $name !== 'disabled']]);
            $enrollments[$name] = Enrollment::query()->create([
                'team_id' => $sequenceTeam->id,
                'sequence_id' => $sequence->id,
                'contact_id' => $contact->id,
                'status' => $name === 'completed' ? 'completed' : 'active',
            ]);
        }

        $this->actingAs($owner)->postJson('/api/v1/crm/sales-engagement/events', ['contact_id' => $contact->id, 'event' => 'reply'])
            ->assertCreated()->assertJsonPath('data.event', 'reply');

        self::assertSame('stopped', $enrollments['enabled']->fresh()->status);
        self::assertSame('active', $enrollments['disabled']->fresh()->status);
        self::assertSame('completed', $enrollments['completed']->fresh()->status);
        self::assertSame('active', $enrollments['foreign']->fresh()->status);
    }

    public function test_manual_stop_cancels_queued_tasks_and_clears_the_schedule(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $contact = Contact::factory()->create(['team_id' => $team->id]);
        $sequence = app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Manual stop', 'timezone' => 'UTC']);
        $enrollment = app(EnrollContact::class)->execute($team->id, $owner->id, ['sequence_id' => $sequence->id, 'contact_id' => $contact->id]);
        $enrollment->update(['next_run_at' => now()->addHour()]);
        $task = EngagementTask::query()->create(['team_id' => $team->id, 'enrollment_id' => $enrollment->id, 'channel' => 'email']);

        app(StopEnrollment::class)->execute($team->id, $owner->id, $enrollment->id, 'manual');
        app(StopEnrollment::class)->execute($team->id, $owner->id, $enrollment->id, 'manual');

        self::assertSame('stopped', $enrollment->fresh()->status);
        self::assertNull($enrollment->fresh()->next_run_at);
        self::assertSame('cancelled', $task->fresh()->status);
    }

    public function test_stop_rules_reject_non_boolean_settings(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);

        $this->expectException(ValidationException::class);
        app(CreateSequence::class)->execute($team->id, $owner->id, ['name' => 'Invalid rules', 'timezone' => 'UTC', 'stop_rules' => ['reply' => 'false']]);
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
