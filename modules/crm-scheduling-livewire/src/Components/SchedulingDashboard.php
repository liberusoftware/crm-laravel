<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Scheduling\Queries\SchedulingQuery;
use Livewire\Component;

final class SchedulingDashboard extends Component
{
    public function render(SchedulingQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-scheduling-livewire::dashboard', ['links' => $query->links((int) $id)->get(), 'bookings' => $query->bookings((int) $id)->limit(25)->get()]);
    }
}
