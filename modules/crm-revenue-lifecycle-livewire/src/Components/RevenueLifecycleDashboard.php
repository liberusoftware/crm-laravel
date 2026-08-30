<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\RevenueLifecycle\Queries\RevenueQuery;
use Livewire\Component;

final class RevenueLifecycleDashboard extends Component
{
    public function render(RevenueQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-revenue-lifecycle-livewire::dashboard', ['assets' => $query->assets((int) $id)->limit(25)->get(), 'orders' => $query->orders((int) $id)->limit(25)->get(), 'fallout' => $query->fallout((int) $id)->limit(25)->get()]);
    }
}
