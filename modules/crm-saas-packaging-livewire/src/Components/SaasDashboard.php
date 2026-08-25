<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\SaasPackaging\Queries\SaasQuery;
use Livewire\Component;

final class SaasDashboard extends Component
{
    public function render(SaasQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-saas-packaging-livewire::dashboard', ['plans' => $query->plans()->get(), 'subscription' => $query->subscription((int) $id), 'usage' => $query->usage((int) $id)->limit(25)->get()]);
    }
}
