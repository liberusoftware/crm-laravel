<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadCapture\Queries\CaptureQuery;
use Livewire\Component;

final class LeadInbox extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-lead-capture::inbox', ['leads' => app(CaptureQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
