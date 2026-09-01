<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ClientOnboarding\Queries\ClientOnboardingQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class OnboardingBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(ClientOnboardingQuery $query): View
    {
        $onboardings = $query->onboardings((int) auth()->user()?->getAttribute('current_team_id'))->when($this->search !== '', fn ($builder) => $builder->where('client_key', 'like', '%'.$this->search.'%'))->when($this->status !== '', fn ($builder) => $builder->where('status', $this->status))->paginate(15);

        return view('crm-client-onboarding::onboarding-browser', ['onboardings' => $onboardings]);
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
