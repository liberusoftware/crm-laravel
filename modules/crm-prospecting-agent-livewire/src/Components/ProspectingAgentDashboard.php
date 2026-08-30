<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ProspectingAgent\Queries\ProspectingAgentQuery;
use Livewire\Component;

final class ProspectingAgentDashboard extends Component
{
    public function render(ProspectingAgentQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-prospecting-agent-livewire::dashboard', ['runs' => $query->runs((int) $id)->limit(25)->get(), 'targets' => $query->targets((int) $id)->limit(25)->get(), 'sequences' => $query->sequences((int) $id)->limit(25)->get()]);
    }
}
