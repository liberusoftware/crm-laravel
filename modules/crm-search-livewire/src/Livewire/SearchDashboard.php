<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchLivewire\Livewire;

use Liberu\CRM\CrmSearch\Queries\CrmSearchQuery;
use Livewire\Component;

final class SearchDashboard extends Component
{
    public string $term = '';

    public function render()
    {
        $user = auth()->user();
        $teamId = (int) $user?->current_team_id;
        $query = app(CrmSearchQuery::class);

        return app('view')->make('module-crm-search-livewire::dashboard', ['results' => $query->search($teamId, $this->term)->limit(25)->get(), 'recents' => $query->recents($teamId, (int) $user?->id)->limit(10)->get()]);
    }
}
