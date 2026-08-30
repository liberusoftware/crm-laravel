<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ServiceAnalytics\Queries\AnalyticsQuery;
use Livewire\Component;

final class AnalyticsDashboard extends Component
{
    public function render(AnalyticsQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-service-analytics-livewire::dashboard', ['summary' => $query->summary((int) $id), 'snapshots' => $query->snapshots((int) $id)->limit(25)->get()]);
    }
}
