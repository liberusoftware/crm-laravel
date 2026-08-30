<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsLivewire\Livewire;

use Liberu\CRM\EventsAndWebinars\Queries\EventQuery;
use Livewire\Component;

final class EventsDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-events-and-webinars-livewire::dashboard', ['events' => app(EventQuery::class)->events($teamId)->limit(25)->get()]);
    }
}
