<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\QuotasAndIncentives\Queries\QuotaQuery;
use Livewire\Component;

final class QuotaDashboard extends Component
{
    public function render(QuotaQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-quotas-and-incentives-livewire::dashboard', ['quotas' => $query->quotas((int) $id)->limit(25)->get(), 'credits' => $query->credits((int) $id)->limit(25)->get(), 'disputes' => $query->disputes((int) $id)->limit(25)->get()]);
    }
}
