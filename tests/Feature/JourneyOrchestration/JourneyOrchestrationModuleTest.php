<?php

declare(strict_types=1);

namespace Tests\Feature\JourneyOrchestration;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\JourneyOrchestration\Actions\CreateJourney;
use Liberu\CRM\JourneyOrchestration\Actions\PublishJourney;
use Liberu\CRM\JourneyOrchestration\Actions\StartJourneyRun;
use Liberu\CRM\JourneyOrchestration\Actions\StopJourneyRun;
use Tests\TestCase;

final class JourneyOrchestrationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_run_and_stop_controls_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $journey = app(CreateJourney::class)->execute($team->id, $owner->id, ['slug' => 'onboarding', 'name' => 'Onboarding', 'trigger_type' => 'event', 'definition' => ['steps' => [['type' => 'wait', 'days' => 1], ['type' => 'goal']]], 'controls' => ['frequency_cap' => 1, 'stop_on_response' => true]]);
        app(PublishJourney::class)->execute($team->id, $owner->id, $journey, ['status' => 'published']);
        $run = app(StartJourneyRun::class)->execute($team->id, $owner->id, $journey, ['subject_id' => $owner->id, 'current_step' => 'wait']);
        app(StopJourneyRun::class)->execute($team->id, $owner->id, $run, ['reason' => 'response']);
        $this->assertDatabaseHas('crm_journeys', ['team_id' => $team->id, 'status' => 'published']);
        $this->assertDatabaseHas('crm_journey_runs', ['team_id' => $team->id, 'status' => 'stopped', 'stop_reason' => 'response']);
        $this->assertDatabaseMissing('crm_journeys', ['team_id' => $other->id, 'slug' => 'onboarding']);
    }
}
