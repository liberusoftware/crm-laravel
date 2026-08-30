<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Forecasting\Models\ForecastCategory;
use Liberu\CRM\Forecasting\Services\ForecastingPolicy;

final class CreateCategory
{
    public function __construct(private readonly ForecastingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ForecastCategory
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:100'], 'code' => ['required', 'string', 'max:40'], 'weight' => ['nullable', 'integer', 'between:0,100'], 'active' => ['nullable', 'boolean']])->validate();

        return ForecastCategory::query()->create(['team_id' => $teamId, ...$data]);
    }
}
