<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting\Actions;

use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Models\ForecastSubmission;
use Liberu\CRM\Forecasting\Services\ForecastingPolicy;

final class SubmitForecast
{
    public function __construct(private readonly ForecastingPolicy $policy) {}

    public function execute(int $teamId, int $userId, Forecast $forecast): ForecastSubmission
    {
        abort_unless($forecast->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);

        return ForecastSubmission::query()->create(['team_id' => $teamId, 'forecast_id' => $forecast->id, 'actor_id' => $userId, 'snapshot' => $forecast->only(['period', 'scenario', 'pipeline', 'best_case', 'commit', 'coverage']), 'submitted_at' => now()]);
    }
}
