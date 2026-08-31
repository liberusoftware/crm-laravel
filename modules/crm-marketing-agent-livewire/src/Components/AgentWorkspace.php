<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\MarketingAgent\Queries\AgentQuery;
use Livewire\Component;

final class AgentWorkspace extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-marketing-agent::workspace', ['requests' => app(AgentQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
