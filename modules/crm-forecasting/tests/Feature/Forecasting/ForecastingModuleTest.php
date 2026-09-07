<?php

declare(strict_types=1);

namespace Tests\Feature\Forecasting;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Forecasting\Actions\AdjustForecast;
use Liberu\CRM\Forecasting\Actions\CreateCategory;
use Liberu\CRM\Forecasting\Actions\RecordForecast;
use Liberu\CRM\Forecasting\Actions\SubmitForecast;
use Liberu\CRM\Forecasting\Queries\ForecastingQuery;
use Tests\TestCase;

final class ForecastingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_forecast_lifecycle_is_scoped_and_submittable(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $category = app(CreateCategory::class)->execute($team->id, $owner->id, ['name' => 'Commit', 'code' => 'commit']);
        $forecast = app(RecordForecast::class)->execute($team->id, $owner->id, ['category_id' => $category->id, 'period' => '2026-Q3', 'commit' => 125000, 'coverage' => 3.5]);
        $submission = app(SubmitForecast::class)->execute($team->id, $owner->id, $forecast);
        $this->assertSame($team->id, $submission->team_id);
        $this->assertSame(125000.0, (float) $forecast->fresh()->commit);
        $this->assertDatabaseHas('crm_forecast_submissions', ['forecast_id' => $forecast->id, 'actor_id' => $owner->id]);

        $adjustment = app(AdjustForecast::class)->execute($team->id, $owner->id, $forecast->id, ['amount' => 5000, 'reason' => 'Late expansion']);
        $this->assertSame(5000.0, (float) $adjustment->amount);
        $this->assertSame(5000.0, app(ForecastingQuery::class)->summary($team->id, '2026-Q3')['adjustments']);

        $owner->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($owner)->postJson("/api/v1/crm/forecasting/forecasts/{$forecast->id}/adjustments", ['amount' => -1000, 'reason' => 'Risk correction'])
            ->assertCreated();
        $this->assertDatabaseHas('crm_forecast_adjustments', ['forecast_id' => $forecast->id, 'amount' => -1000, 'reason' => 'Risk correction']);
    }

    public function test_forecast_category_and_adjustment_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $otherTeam = Team::factory()->create(['user_id' => $owner->id]);
        $category = app(CreateCategory::class)->execute($otherTeam->id, $owner->id, ['name' => 'Other', 'code' => 'other']);

        $this->expectException(ValidationException::class);
        app(RecordForecast::class)->execute($team->id, $owner->id, ['category_id' => $category->id, 'period' => '2026-Q3']);
    }
}
