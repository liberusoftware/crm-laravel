<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\WebIntent\Actions\ResolveAlert;
use Liberu\CRM\WebIntent\Models\WebIntentAlert;
use Liberu\CRM\WebIntent\Queries\WebIntentQuery;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;
use Livewire\Component;
use Livewire\WithPagination;

final class IntentDashboard extends Component
{
    use WithPagination;

    public string $intentLevel = '';

    public string $search = '';

    public function resolve(int $id, ResolveAlert $resolve, WebIntentPolicy $policy): void
    {
        $alert = WebIntentAlert::query()->where('team_id', $this->teamId())->findOrFail($id);
        $resolve->execute($alert, (int) auth()->id(), $policy);
        $this->dispatch('web-intent-alert-resolved');
    }

    public function render(WebIntentQuery $query): View
    {
        $visits = $query->visits($this->teamId())->when($this->intentLevel !== '', fn ($builder) => $builder->where('intent_level', $this->intentLevel))->when($this->search !== '', fn ($builder) => $builder->where('visitor_key', 'like', '%'.addcslashes($this->search, '%_').'%'))->latest()->paginate(25);

        return app('view')->make('crm-web-intent-livewire::dashboard', ['summary' => $query->summary($this->teamId()), 'visits' => $visits, 'alerts' => $query->alerts($this->teamId())->where('status', 'open')->latest()->limit(10)->get()]);
    }

    private function teamId(): int
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return (int) $id;
    }
}
