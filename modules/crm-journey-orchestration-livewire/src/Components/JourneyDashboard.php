<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\JourneyOrchestration\Queries\JourneyQuery;
use Livewire\Component;

final class JourneyDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-journey-orchestration::dashboard', ['journeys' => app(JourneyQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
