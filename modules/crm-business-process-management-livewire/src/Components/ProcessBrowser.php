<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\BusinessProcessManagement\Queries\ProcessQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class ProcessBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(ProcessQuery $query): View
    {
        $processes = $query->processes((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where(fn ($nested) => $nested->where('name', 'like', '%'.$this->search.'%')->orWhere('key', 'like', '%'.$this->search.'%')))->when($this->status !== '', fn ($builder) => $builder->where('status', $this->status))->paginate(15);

        return view('crm-business-process-management::process-browser', ['processes' => $processes]);
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
