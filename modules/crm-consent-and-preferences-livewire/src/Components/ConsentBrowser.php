<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;
use Livewire\Component;
use Livewire\WithPagination;

final class ConsentBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $records = ConsentRecord::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
            $query->where('subject_type', 'like', '%'.addcslashes($this->search, '%_').'%')->orWhere('channel', 'like', '%'.addcslashes($this->search, '%_').'%')->orWhere('topic', 'like', '%'.addcslashes($this->search, '%_').'%');
        }))->latest()->paginate(25);

        return app('view')->make('crm-consent-and-preferences-livewire::consent-browser', ['records' => $records]);
    }
}
