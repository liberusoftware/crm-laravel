<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PredictiveModels\Models\ModelEvaluation;
use Liberu\CRM\PredictiveModels\Models\PredictiveModel;
use Liberu\CRM\PredictiveModels\Services\PredictiveModelPolicy;

final class RecordEvaluation
{
    public function __construct(private readonly PredictiveModelPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ModelEvaluation
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['model_id' => ['required', 'integer'], 'accuracy' => ['nullable', 'numeric', 'between:0,1'], 'precision_score' => ['nullable', 'numeric', 'between:0,1'], 'recall' => ['nullable', 'numeric', 'between:0,1'], 'metrics' => ['nullable', 'array']])->validate();
        PredictiveModel::query()->where('team_id', $teamId)->findOrFail($data['model_id']);

        return ModelEvaluation::query()->create(['team_id' => $teamId, ...$data, 'evaluated_at' => now()]);
    }
}
