<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\TerritoriesAndOwnership\Queries\TerritoryQuery;
use Livewire\Component;

final class TerritoryDashboard extends Component
{
    public function render(TerritoryQuery $q): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-territories-and-ownership-livewire::dashboard', ['rules' => $q->rules((int) $id)]);
    }
}
