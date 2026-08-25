<?php

declare(strict_types=1);

namespace Tests\Feature\PredictiveModels;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\PredictiveModels\Actions\DetectDrift;
use Liberu\CRM\PredictiveModels\Actions\RecordPrediction;
use Liberu\CRM\PredictiveModels\Actions\RegisterPredictiveModel;
use Liberu\CRM\PredictiveModels\Models\ModelDrift;
use Liberu\CRM\PredictiveModels\Models\Prediction;
use Tests\TestCase;

final class PredictiveModelsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_explained_predictions_and_drift_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $other = Team::factory()->create(['user_id' => User::factory()->create()->id]);
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $model = app(RegisterPredictiveModel::class)->execute($team->id, $owner->id, ['name' => 'Opportunity score', 'kind' => 'scoring', 'version' => '1.0']);
        $model->update(['status' => 'active']);
        $prediction = app(RecordPrediction::class)->execute($team->id, $owner->id, ['model_id' => $model->id, 'subject_type' => 'opportunity', 'subject_id' => 5, 'kind' => 'scoring', 'score' => 0.82, 'explanation' => ['engagement' => 0.4]]);
        $drift = app(DetectDrift::class)->execute($team->id, $owner->id, ['model_id' => $model->id, 'feature' => 'engagement', 'baseline' => 0.4, 'observed' => 0.9, 'threshold' => 0.2]);

        self::assertSame(0.82, $prediction->score);
        self::assertSame('drifted', $drift->status);
        self::assertCount(1, Prediction::query()->where('team_id', $team->id)->get());
        self::assertCount(1, ModelDrift::query()->where('team_id', $team->id)->get());
        self::assertCount(0, Prediction::query()->where('team_id', $other->id)->get());
    }
}
