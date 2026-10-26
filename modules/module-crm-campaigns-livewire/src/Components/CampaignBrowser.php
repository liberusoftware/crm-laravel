<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Campaigns\Queries\CampaignQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class CampaignBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(CampaignQuery $query): View
    {
        $campaigns = $query->campaigns((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))->paginate(15);

        return view('crm-campaigns::campaign-browser', ['campaigns' => $campaigns]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
}
