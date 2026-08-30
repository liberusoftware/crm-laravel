<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ReputationManagement\Queries\ReputationQuery;
use Livewire\Component;

final class ReputationDashboard extends Component
{
    public function render(ReputationQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-reputation-management-livewire::dashboard', ['reviews' => $query->reviews((int) $id)->limit(25)->get(), 'requests' => $query->requests((int) $id)->limit(25)->get(), 'connections' => $query->connections((int) $id)->get()]);
    }
}
