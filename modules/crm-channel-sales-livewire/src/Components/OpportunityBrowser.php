<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ChannelSales\Queries\ChannelSalesQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class OpportunityBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(ChannelSalesQuery $query): View
    {
        $opportunities = $query->opportunities((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where(fn ($nested) => $nested->where('partner_key', 'like', '%'.$this->search.'%')->orWhere('opportunity_key', 'like', '%'.$this->search.'%')))->paginate(15);

        return view('crm-channel-sales::opportunity-browser', ['opportunities' => $opportunities]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
