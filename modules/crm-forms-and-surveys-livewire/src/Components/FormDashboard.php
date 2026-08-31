<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\FormsAndSurveys\Queries\FormQuery;
use Livewire\Component;

final class FormDashboard extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-forms-and-surveys::dashboard', ['forms' => app(FormQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
