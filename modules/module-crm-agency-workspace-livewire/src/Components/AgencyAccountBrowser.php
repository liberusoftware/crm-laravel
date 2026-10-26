<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\AgencyWorkspace\Queries\AgencyQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class AgencyAccountBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(AgencyQuery $query): View
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);
        $search = trim($this->search);
        $accounts = $query->accounts((int) $teamId)
            ->when($search !== '', fn ($builder) => $builder->where('name', 'like', '%'.addcslashes($search, '%_').'%'))
            ->when($this->status !== '', fn ($builder) => $builder->where('status', $this->status))
            ->paginate(25);

        return view('crm-agency-workspace-livewire::account-browser', compact('accounts'));
    }
}
