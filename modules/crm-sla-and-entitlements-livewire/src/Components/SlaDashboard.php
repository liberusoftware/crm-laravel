<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SlaAndEntitlements\Queries\SlaQuery;
use Livewire\Component;

final class SlaDashboard extends Component
{
    public function render(SlaQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-sla-and-entitlements-livewire::dashboard', ['cases' => $query->cases((int) $id)->limit(25)->get(), 'contracts' => $query->contracts((int) $id)->get()]);
    }
}
