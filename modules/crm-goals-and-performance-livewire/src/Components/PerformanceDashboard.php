<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\GoalsAndPerformance\Queries\PerformanceQuery;
use Livewire\Component;

final class PerformanceDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-goals-and-performance::dashboard', ['goals' => app(PerformanceQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
