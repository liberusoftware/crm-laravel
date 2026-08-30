<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SandboxAndReleaseManagement\Queries\ReleaseQuery;
use Livewire\Component;

final class ReleaseDashboard extends Component
{
    public function render(ReleaseQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sandbox-and-release-management-livewire::dashboard', ['changesets' => $query->changesets((int) $id)->limit(25)->get(), 'deployments' => $query->deployments((int) $id)->limit(25)->get()]);
    }
}
