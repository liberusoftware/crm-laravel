<?php

declare(strict_types=1);

namespace Tests\Feature\GoalsAndPerformance;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\GoalsAndPerformance\Actions\CreateGoal;
use Liberu\CRM\GoalsAndPerformance\Actions\RecordReview;
use Liberu\CRM\GoalsAndPerformance\Actions\UpdateGoalActual;
use Tests\TestCase;

final class GoalsAndPerformanceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_scorecard_review_and_coaching_history_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $goal = app(CreateGoal::class)->execute($team->id, $owner->id, ['owner_id' => $owner->id, 'scope' => 'individual', 'name' => 'Pipeline', 'target' => 100, 'starts_on' => now()->toDateString()]);
        app(UpdateGoalActual::class)->execute($team->id, $owner->id, $goal, ['kind' => 'outcome', 'value' => 40, 'notes' => 'Quarter progress']);
        app(RecordReview::class)->execute($team->id, $owner->id, ['subject_id' => $owner->id, 'reviewer_id' => $owner->id, 'period' => '2026-Q3', 'status' => 'completed', 'score' => 85, 'coaching_plan' => ['focus' => 'discovery']]);
        $this->assertDatabaseHas('crm_performance_goals', ['team_id' => $team->id, 'actual' => '40.00']);
        $this->assertDatabaseHas('crm_performance_reviews', ['team_id' => $team->id, 'score' => 85]);
        $this->assertDatabaseMissing('crm_performance_goals', ['team_id' => $other->id, 'name' => 'Pipeline']);
    }
}
