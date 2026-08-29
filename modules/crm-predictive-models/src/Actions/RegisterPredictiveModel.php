<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PredictiveModels\Models\PredictiveModel;
use Liberu\CRM\PredictiveModels\Services\PredictiveModelPolicy;

final class RegisterPredictiveModel
{
    public function __construct(private readonly PredictiveModelPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PredictiveModel
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:scoring,churn,next_action,next_product,forecast,routing'], 'version' => ['required', 'string', 'max:50'], 'configuration' => ['nullable', 'array']])->validate();

        return PredictiveModel::query()->create(['team_id' => $teamId, ...$data]);
    }
}
