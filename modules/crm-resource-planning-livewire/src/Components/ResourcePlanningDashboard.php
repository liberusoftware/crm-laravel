<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ResourcePlanning\Queries\ResourcePlanningQuery;
use Livewire\Component;

final class ResourcePlanningDashboard extends Component
{
    public function render(ResourcePlanningQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-resource-planning-livewire::dashboard', ['capacity' => $query->capacity((int) $id)->limit(25)->get(), 'bookings' => $query->bookings((int) $id)->limit(25)->get(), 'forecasts' => $query->forecasts((int) $id)->limit(25)->get()]);
    }
}
