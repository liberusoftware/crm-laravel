<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadQualification\Queries\LeadQualificationQuery;
use Livewire\Component;

final class QualificationDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-lead-qualification::dashboard', ['leads' => app(LeadQualificationQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
