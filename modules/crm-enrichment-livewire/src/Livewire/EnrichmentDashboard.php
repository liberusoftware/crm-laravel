<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentLivewire\Livewire;

use Liberu\CRM\Enrichment\Queries\EnrichmentQuery;
use Livewire\Component;

final class EnrichmentDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-enrichment-livewire::dashboard', ['profiles' => app(EnrichmentQuery::class)->profiles($teamId)->limit(25)->get()]);
    }
}
