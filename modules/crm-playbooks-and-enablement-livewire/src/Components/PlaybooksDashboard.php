<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\PlaybooksAndEnablement\Queries\PlaybookQuery;
use Livewire\Component;

final class PlaybooksDashboard extends Component
{
    public function render(PlaybookQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-playbooks-and-enablement-livewire::dashboard', ['playbooks' => $query->playbooks((int) $id)->get(), 'assignments' => $query->assignments((int) $id)->limit(25)->get(), 'recommendations' => $query->recommendations((int) $id)->limit(25)->get()]);
    }
}
