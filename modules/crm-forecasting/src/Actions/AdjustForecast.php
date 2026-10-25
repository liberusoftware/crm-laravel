<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Models\ForecastAdjustment;
use Liberu\CRM\Forecasting\Services\ForecastingPolicy;

final class AdjustForecast
{
    public function __construct(private readonly ForecastingPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $forecastId, array $input): ForecastAdjustment
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        if (isset($input['reason']) && is_string($input['reason'])) {
            $input['reason'] = trim($input['reason']);
        }
        $data = Validator::make($input, [
            'amount' => ['required', 'numeric', 'decimal:0,2', 'between:-99999999999999.99,99999999999999.99'],
            'reason' => ['required', 'string', 'max:5000'],
        ])->validate();

        return DB::transaction(function () use ($teamId, $userId, $forecastId, $data): ForecastAdjustment {
            $forecast = Forecast::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($forecastId);

            return ForecastAdjustment::query()->create([
                'team_id' => $teamId, 'forecast_id' => $forecast->getKey(), 'actor_id' => $userId,
                'amount' => $data['amount'], 'reason' => $data['reason'],
            ]);
        });
    }
}
