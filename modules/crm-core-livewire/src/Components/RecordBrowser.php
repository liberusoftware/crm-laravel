<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Core\Models\Record;
use Livewire\Component;
use Livewire\WithPagination;

final class RecordBrowser extends Component
{
    use WithPagination;

    public string $type = 'contact';

    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $records = Record::query()->where('team_id', $teamId)->where('record_type', $this->type)->active()->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.addcslashes($this->search, '%_').'%'))->latest()->paginate(25);

        return app('view')->make('crm-core-livewire::record-browser', ['records' => $records]);
    }
}
