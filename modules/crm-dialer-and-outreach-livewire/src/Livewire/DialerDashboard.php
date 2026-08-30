<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachLivewire\Livewire;

use Liberu\CRM\DialerAndOutreach\Queries\DialerQuery;
use Livewire\Component;

final class DialerDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-dialer-and-outreach-livewire::dashboard', ['lists' => app(DialerQuery::class)->lists($teamId)->limit(25)->get()]);
    }
}
