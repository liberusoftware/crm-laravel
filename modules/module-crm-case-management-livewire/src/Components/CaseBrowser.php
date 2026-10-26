<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\CaseManagement\Queries\CaseQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class CaseBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'open';

    public function render(CaseQuery $query): View
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');
        $cases = $query->queue($teamId, $this->status)->when($this->search !== '', fn ($builder) => $builder->where(fn ($nested) => $nested->where('case_key', 'like', '%'.$this->search.'%')->orWhere('subject', 'like', '%'.$this->search.'%')))->paginate(15);

        return view('crm-case-management::case-browser', ['cases' => $cases]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }
}
