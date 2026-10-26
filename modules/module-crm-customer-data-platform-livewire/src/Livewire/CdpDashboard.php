<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformLivewire\Livewire;

use Liberu\CRM\CustomerDataPlatform\Queries\CdpQuery;
use Livewire\Component;

final class CdpDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;
        $query = app(CdpQuery::class);

        return app('view')->make('module-crm-customer-data-platform-livewire::dashboard', ['profiles' => $query->profiles($teamId)->limit(25)->get(), 'audiences' => $query->audiences($teamId)->limit(25)->get()]);
    }
}
