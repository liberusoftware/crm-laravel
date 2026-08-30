<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SalesEngagement\Queries\EngagementQuery;
use Livewire\Component;

final class EngagementDashboard extends Component
{
    public function render(EngagementQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sales-engagement-livewire::dashboard', ['sequences' => $query->sequences((int) $id)->get(), 'enrollments' => $query->enrollments((int) $id)->limit(25)->get(), 'tasks' => $query->tasks((int) $id)->limit(25)->get()]);
    }
}
