<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingLivewire\Livewire;

use Liberu\CRM\Forecasting\Queries\ForecastingQuery;
use Livewire\Component;

final class ForecastDashboard extends Component
{
    public string $period = 'current';

    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-forecasting-livewire::forecast-dashboard', ['summary' => app(ForecastingQuery::class)->summary($teamId, $this->period)]);
    }
}
