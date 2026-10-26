<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Collaboration\Queries\CollaborationQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class CollaborationBrowser extends Component
{
    use WithPagination;

    public string $queue = 'default';

    public string $search = '';

    public function render(CollaborationQuery $query): View
    {
        $work = $query->queue((int) auth()->user()?->getAttribute('current_team_id'), $this->queue)->when($this->search !== '', fn ($builder) => $builder->where('subject_key', 'like', '%'.$this->search.'%'))->paginate(15);

        return view('crm-collaboration::collaboration-browser', ['work' => $work]);
    }

    public function updatedQueue(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
