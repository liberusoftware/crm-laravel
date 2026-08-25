<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Queries;

use Liberu\CRM\Forecasting\Models\Forecast;
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
        return Forecast::query()->where('team_id', $teamId)->where('period', $period)->selectRaw('sum(pipeline) pipeline,sum(best_case) best_case,sum(commit) commit,avg(coverage) coverage')->first()->toArray();
    }
}
