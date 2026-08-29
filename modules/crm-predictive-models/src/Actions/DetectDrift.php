<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PredictiveModels\Models\ModelDrift;
use Liberu\CRM\PredictiveModels\Models\PredictiveModel;
use Liberu\CRM\PredictiveModels\Services\PredictiveModelPolicy;

final class DetectDrift
{
    public function __construct(private readonly PredictiveModelPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ModelDrift
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['model_id' => ['required', 'integer'], 'feature' => ['required', 'string', 'max:255'], 'baseline' => ['required', 'numeric'], 'observed' => ['required', 'numeric'], 'threshold' => ['required', 'numeric', 'min:0']])->validate();
        PredictiveModel::query()->where('team_id', $teamId)->findOrFail($data['model_id']);
        $status = abs((float) $data['observed'] - (float) $data['baseline']) > (float) $data['threshold'] ? 'drifted' : 'normal';

        return ModelDrift::query()->create(['team_id' => $teamId, ...$data, 'status' => $status, 'detected_at' => now()]);
    }
}
