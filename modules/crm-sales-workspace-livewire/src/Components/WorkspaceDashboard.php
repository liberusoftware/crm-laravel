<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SalesWorkspace\Queries\WorkspaceQuery;
use Livewire\Component;

final class WorkspaceDashboard extends Component
{
    public function render(WorkspaceQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sales-workspace-livewire::dashboard', ['feed' => $query->feed((int) $id)->limit(25)->get(), 'overdue' => $query->overdue((int) $id)->get(), 'agenda' => $query->agenda((int) $id)->get()]);
    }
}
