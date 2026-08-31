<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Loyalty\Queries\LoyaltyQuery;
use Livewire\Component;

final class LoyaltyDashboard extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-loyalty::dashboard', ['members' => app(LoyaltyQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
