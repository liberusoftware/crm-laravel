<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessLivewire\Livewire;

use Liberu\CRM\CustomerSuccess\Queries\CustomerSuccessQuery;
use Livewire\Component;

final class SuccessDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-customer-success-livewire::dashboard', ['customers' => app(CustomerSuccessQuery::class)->customers($teamId)->limit(25)->get(), 'risks' => app(CustomerSuccessQuery::class)->risks($teamId)->limit(25)->get()]);
    }
}
