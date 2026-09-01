<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Livewire\Component;
use Livewire\WithPagination;

final class AgentBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(): View
    {
        $query = ReceptionAgent::query()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        return view('crm-ai-reception-and-conversation::agent-browser', ['agents' => $query->latest()->paginate(15)]);
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
