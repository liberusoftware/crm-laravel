<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\RevenueIntelligence\Queries\RevenueIntelligenceQuery;
use Livewire\Component;

final class RevenueIntelligenceDashboard extends Component
{
    public function render(RevenueIntelligenceQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-revenue-intelligence-livewire::dashboard', ['insights' => $query->insights((int) $id)->limit(25)->get(), 'alerts' => $query->alerts((int) $id)->limit(25)->get()]);
    }
}
