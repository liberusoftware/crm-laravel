<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\PartnerRelationshipManagement\Queries\PartnerQuery;
use Livewire\Component;

final class PartnersDashboard extends Component
{
    public function render(PartnerQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-partner-relationship-management-livewire::dashboard', ['partners' => $query->partners((int) $id)->limit(25)->get(), 'activities' => $query->activities((int) $id)->limit(25)->get(), 'performance' => $query->performance((int) $id)->limit(25)->get()]);
    }
}
