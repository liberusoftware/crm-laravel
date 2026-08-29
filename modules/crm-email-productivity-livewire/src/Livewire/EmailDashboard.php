<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityLivewire\Livewire;

use Liberu\CRM\EmailProductivity\Queries\EmailProductivityQuery;
use Livewire\Component;

final class EmailDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;
        $query = app(EmailProductivityQuery::class);

        return app('view')->make('module-crm-email-productivity-livewire::dashboard', ['mailboxes' => $query->mailboxes($teamId)->get(), 'messages' => $query->messages($teamId)->limit(25)->get()]);
    }
}
