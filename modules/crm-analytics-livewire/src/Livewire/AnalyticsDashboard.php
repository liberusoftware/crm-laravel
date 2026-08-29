<?php

declare(strict_types=1);

namespace Liberu\CRM\AnalyticsLivewire\Livewire;

use Liberu\CRM\Analytics\Queries\AnalyticsQuery;
use Livewire\Component;

final class AnalyticsDashboard extends Component
{
    public function render(): mixed
    {
        return app('view')->make('module-crm-analytics-livewire::dashboard', ['assets' => app(AnalyticsQuery::class)->assets((int) auth()->user()->current_team_id)->get()]);
    }
}
