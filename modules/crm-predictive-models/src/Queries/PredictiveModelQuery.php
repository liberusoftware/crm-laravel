<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Queries;

use Liberu\CRM\PredictiveModels\Models\ModelDrift;
use Liberu\CRM\PredictiveModels\Models\ModelEvaluation;
use Liberu\CRM\PredictiveModels\Models\Prediction;
use Liberu\CRM\PredictiveModels\Models\PredictiveModel;

final class PredictiveModelQuery
{
    public function models(int $teamId)
    {
        return PredictiveModel::query()->where('team_id', $teamId)->latest();
    }

    public function predictions(int $teamId)
    {
        return Prediction::query()->where('team_id', $teamId)->latest();
    }

    public function evaluations(int $teamId)
    {
        return ModelEvaluation::query()->where('team_id', $teamId)->latest();
    }

    public function drift(int $teamId)
    {
        return ModelDrift::query()->where('team_id', $teamId)->latest();
    }
}
