<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\WorkManagement\Actions\CompleteWorkItem;
use Liberu\CRM\WorkManagement\Actions\CreateWorkItem;
use Liberu\CRM\WorkManagement\Models\WorkItem;
use Livewire\Component;
use Livewire\WithPagination;

final class WorkBoard extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $view = 'team';

    public string $newTitle = '';

    public function save(CreateWorkItem $create): void
    {
        $teamId = $this->teamId();
        $this->validate(['newTitle' => ['required', 'string', 'max:200']]);
        $create->execute($teamId, auth()->id(), ['title' => $this->newTitle]);
        $this->reset('newTitle');
        $this->resetPage();
        $this->dispatch('work-item-created');
    }

    public function complete(int $id, CompleteWorkItem $complete): void
    {
        $this->validate(['id' => ['nullable', 'integer', 'min:1']]);
        $item = WorkItem::query()->where('team_id', $this->teamId())->findOrFail($id);
        $complete->execute($item, auth()->id(), $item->version);
        $this->dispatch('work-item-completed');
    }

    public function render(): View
    {
        $teamId = $this->teamId();
        $items = WorkItem::query()->where('team_id', $teamId)->when($this->view === 'personal', fn ($query) => $query->where('assigned_to', auth()->id()))->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.addcslashes($this->search, '%_').'%'))->when($this->status !== '', fn ($query) => $query->where('status', $this->status))->latest()->paginate(25);

        return app('view')->make('crm-work-management-livewire::work-board', ['items' => $items]);
    }

    private function teamId(): int
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return (int) $teamId;
    }
}
