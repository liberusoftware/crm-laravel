<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Activities\Models\Activity;
use Livewire\Component;
use Livewire\WithPagination;

final class ActivityBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $kind = '';

    public string $status = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $activities = Activity::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.addcslashes($this->search, '%_').'%'))->when($this->kind !== '', fn ($query) => $query->where('kind', $this->kind))->when($this->status !== '', fn ($query) => $query->where('status', $this->status))->latest('due_at')->paginate(25);

        return app('view')->make('crm-activities-livewire::activity-browser', ['activities' => $activities]);
    }
}
