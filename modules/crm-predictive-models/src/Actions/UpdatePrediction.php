<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PredictiveModels\Models\Prediction;
use Liberu\CRM\PredictiveModels\Models\PredictiveModel;
use Liberu\CRM\PredictiveModels\Services\PredictiveModelPolicy;

final class UpdatePrediction
{
    public function __construct(private readonly PredictiveModelPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $predictionId, array $input): Prediction
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'model_id' => ['required', 'integer'],
            'subject_type' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'integer'],
            'kind' => ['required', 'in:scoring,churn,next_action,next_product,forecast,routing'],
            'score' => ['nullable', 'numeric', 'between:0,1'],
            'label' => ['nullable', 'string', 'max:255'],
            'explanation' => ['required', 'array'],
            'features' => ['nullable', 'array'],
        ])->validate();
        PredictiveModel::query()->where('team_id', $teamId)->where('status', 'active')->findOrFail($data['model_id']);
        $prediction = Prediction::query()->where('team_id', $teamId)->findOrFail($predictionId);
        $prediction->update($data);

        return $prediction->refresh();
    }
}
