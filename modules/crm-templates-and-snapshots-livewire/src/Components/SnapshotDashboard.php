<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\TemplatesAndSnapshots\Queries\SnapshotQuery;
use Livewire\Component;

final class SnapshotDashboard extends Component
{
    public function render(SnapshotQuery $q): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-templates-and-snapshots-livewire::dashboard', ['snapshots' => $q->list((int) $id)]);
    }
}
