<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationLivewire\Livewire;

use Liberu\CRM\DealRegistration\Queries\DealRegistrationQuery;
use Livewire\Component;

final class DealDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-deal-registration-livewire::dashboard', ['deals' => app(DealRegistrationQuery::class)->deals($teamId)->limit(25)->get()]);
    }
}
