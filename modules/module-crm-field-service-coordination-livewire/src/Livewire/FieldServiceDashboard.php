<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationLivewire\Livewire;

use Liberu\CRM\FieldServiceCoordination\Queries\FieldServiceQuery;
use Livewire\Component;

final class FieldServiceDashboard extends Component
{
    public function render()
    {
        $teamId = (int) auth()->user()?->current_team_id;

        return app('view')->make('module-crm-field-service-coordination-livewire::dashboard', ['appointments' => app(FieldServiceQuery::class)->appointments($teamId)->limit(25)->get()]);
    }
}
