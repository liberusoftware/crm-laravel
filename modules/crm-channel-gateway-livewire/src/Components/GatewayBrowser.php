<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ChannelGateway\Queries\GatewayQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class GatewayBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(GatewayQuery $query): View
    {
        $channels = $query->channels((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where(fn ($nested) => $nested->where('key', 'like', '%'.$this->search.'%')->orWhere('provider', 'like', '%'.$this->search.'%')))->paginate(15);

        return view('crm-channel-gateway::gateway-browser', ['channels' => $channels]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
