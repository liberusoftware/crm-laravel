<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Projects\Queries\ProjectQuery;
use Livewire\Component;

final class ProjectsDashboard extends Component
{
    public function render(ProjectQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-projects-livewire::dashboard', ['projects' => $query->projects((int) $id)->limit(25)->get(), 'tasks' => $query->tasks((int) $id)->limit(25)->get(), 'risks' => $query->risks((int) $id)->limit(25)->get()]);
    }
}
