<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceLivewire\Livewire;

use Liberu\CRM\CustomerSelfService\Queries\SelfServiceQuery;
use Livewire\Component;

final class SelfServiceDashboard extends Component
{
    public string $search = '';

    public function render()
    {
        $user = auth()->user();
        $teamId = (int) $user?->current_team_id;
        $query = app(SelfServiceQuery::class);
        $profile = $query->profile($teamId, (int) $user?->id);

        return app('view')->make('module-crm-customer-self-service-livewire::dashboard', ['profile' => $profile, 'cases' => $profile === null ? collect() : $query->cases($teamId, $profile->id)->limit(25)->get(), 'knowledge' => $query->search($teamId, $this->search)->limit(10)->get()]);
    }
}
