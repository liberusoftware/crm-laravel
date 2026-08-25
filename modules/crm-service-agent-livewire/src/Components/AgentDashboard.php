<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ServiceAgent\Queries\AgentQuery;
use Livewire\Component;

final class AgentDashboard extends Component
{
    public function render(AgentQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-service-agent-livewire::dashboard', ['cases' => $query->cases((int) $id)->limit(25)->get()]);
    }
}
