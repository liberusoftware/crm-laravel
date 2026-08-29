<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LandingPagesAndFunnels\Queries\FunnelQuery;
use Livewire\Component;

final class FunnelDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-landing-pages-and-funnels::dashboard', ['funnels' => app(FunnelQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
