<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\MarketingDevelopmentFunds\Queries\MdfQuery;
use Livewire\Component;

final class MdfDashboard extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-marketing-development-funds::dashboard', ['funds' => app(MdfQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
