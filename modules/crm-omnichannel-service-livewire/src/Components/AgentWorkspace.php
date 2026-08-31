<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\OmnichannelService\Queries\ConversationQuery;
use Livewire\Component;

final class AgentWorkspace extends Component
{
    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $teamId > 0, 403);

        return app('view')->make('module-crm-omnichannel-service::workspace', ['conversations' => app(ConversationQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
