<?php

declare(strict_types=1);

namespace Tests\Feature\Forecasting;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\Forecasting\Actions\CreateCategory;
use Liberu\CRM\Forecasting\Actions\RecordForecast;
use Liberu\CRM\Forecasting\Actions\SubmitForecast;
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
    }
}
