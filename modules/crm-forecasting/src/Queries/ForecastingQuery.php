<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Queries;

use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Models\ForecastAdjustment;
use Liberu\CRM\Forecasting\Models\ForecastCategory;

final class ForecastingQuery
{
    public function categories(int $teamId)
    {
        return ForecastCategory::query()->where('team_id', $teamId)->where('active', true)->orderBy('weight', 'desc');
    }

    public function forecasts(int $teamId, string $period)
    {
        return Forecast::query()->where('team_id', $teamId)->where('period', $period)->latest();
    }

    public function summary(int $teamId, string $period): array
    {
        $forecasts = Forecast::query()->where('team_id', $teamId)->where('period', $period);
        $summary = [
            'pipeline' => (float) (clone $forecasts)->sum('pipeline'),
            'best_case' => (float) (clone $forecasts)->sum('best_case'),
            'commit' => (float) (clone $forecasts)->sum('commit'),
            'coverage' => (float) (clone $forecasts)->avg('coverage'),
        ];
        $summary['adjustments'] = (float) ForecastAdjustment::query()
            ->join('crm_forecasts', 'crm_forecasts.id', '=', 'crm_forecast_adjustments.forecast_id')
            ->where('crm_forecast_adjustments.team_id', $teamId)->where('crm_forecasts.period', $period)
            ->sum('crm_forecast_adjustments.amount');

        return $summary;
    }
}
