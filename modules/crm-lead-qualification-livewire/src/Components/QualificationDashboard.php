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
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return view('crm-lead-qualification-livewire::dashboard', [
            'leads' => app(LeadQualificationQuery::class)->forTeam((int) $teamId)->paginate(25),
        ]);
    }
}
