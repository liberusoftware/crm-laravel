<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Prospecting\Queries\ProspectingQuery;
use Livewire\Component;

final class ProspectingDashboard extends Component
{
    public function render(ProspectingQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-prospecting-livewire::dashboard', ['profiles' => $query->profiles((int) $id)->get(), 'prospects' => $query->prospects((int) $id)->limit(25)->get(), 'queue' => $query->queue((int) $id)->limit(25)->get()]);
    }
}
