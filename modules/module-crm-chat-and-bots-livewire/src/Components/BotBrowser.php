<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ChatAndBots\Queries\ChatBotQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class BotBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(ChatBotQuery $query): View
    {
        $bots = $query->bots((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))->paginate(15);

        return view('crm-chat-and-bots::bot-browser', ['bots' => $bots]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
