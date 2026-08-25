<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Services\ForecastingPolicy;

final class RecordForecast
{
    public function __construct(private readonly ForecastingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Forecast
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['category_id' => ['required', 'integer', 'exists:crm_forecast_categories,id'], 'period' => ['required', 'string', 'max:30'], 'scenario' => ['nullable', 'in:base,upside,downside'], 'pipeline' => ['nullable', 'numeric', 'min:0'], 'best_case' => ['nullable', 'numeric', 'min:0'], 'commit' => ['nullable', 'numeric', 'min:0'], 'coverage' => ['nullable', 'numeric', 'min:0']])->validate();
        $data['team_id'] = $teamId;

        return Forecast::query()->updateOrCreate(['team_id' => $teamId, 'category_id' => $data['category_id'], 'period' => $data['period'], 'scenario' => $data['scenario'] ?? 'base'], $data);
    }
}
