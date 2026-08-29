<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ProposalsAndQuotes\Queries\ProposalQuery;
use Livewire\Component;

final class ProposalDashboard extends Component
{
    public function render(ProposalQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-proposals-and-quotes-livewire::dashboard', ['proposals' => $query->proposals((int) $id)->limit(25)->get(), 'versions' => $query->versions((int) $id)->limit(25)->get(), 'templates' => $query->templates((int) $id)->get()]);
    }
}
