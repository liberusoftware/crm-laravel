<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Contracts\Queries\ContractQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class ContractBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(ContractQuery $query): View
    {
        $contracts = $query->contracts((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))->when($this->status !== '', fn ($builder) => $builder->where('status', $this->status))->paginate(15);

        return view('crm-contracts::contract-browser', ['contracts' => $contracts]);
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
