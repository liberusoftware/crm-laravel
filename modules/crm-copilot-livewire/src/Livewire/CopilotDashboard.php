<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotLivewire\Livewire;

use Liberu\CRM\Copilot\Queries\CopilotQuery;
use Livewire\Component;

final class CopilotDashboard extends Component
{
    public string $input = '';

    public function render()
    {
        $user = auth()->user();

        return app('view')->make('module-crm-copilot-livewire::dashboard', ['requests' => app(CopilotQuery::class)->requests((int) $user?->current_team_id, (int) $user?->id)->limit(10)->get()]);
    }
}
