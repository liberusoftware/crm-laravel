<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Communities\Queries\CommunityQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class CommunityBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(CommunityQuery $query): View
    {
        $spaces = $query->spaces((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))->paginate(15);
        return view('crm-communities::community-browser', ['spaces' => $spaces]);
    }

    public function updatedSearch(): void { $this->resetPage(); }
}
