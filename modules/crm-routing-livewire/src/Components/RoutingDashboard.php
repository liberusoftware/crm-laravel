<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Routing\Queries\RoutingQuery;
use Livewire\Component;

final class RoutingDashboard extends Component
{
    public function render(RoutingQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-routing-livewire::dashboard', ['agents' => $query->agents((int) $id)->get(), 'assignments' => $query->assignments((int) $id)->limit(25)->get()]);
    }
}
