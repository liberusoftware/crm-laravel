<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackLivewire\Livewire;

use Liberu\CRM\AutomationPack\Queries\AutomationPackQuery;
use Livewire\Component;

final class AutomationDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-automation-pack-livewire::dashboard', ['recipes' => app(AutomationPackQuery::class)->recipes($teamId)->limit(25)->get()]);
    }
}
