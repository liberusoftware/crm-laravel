<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\DataOperations\Models\DataOperation;
use Livewire\Component;
use Livewire\WithPagination;

final class OperationBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $operations = DataOperation::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where('source', 'like', '%'.addcslashes($this->search, '%_').'%'))->when($this->status !== '', fn ($query) => $query->where('status', $this->status))->latest()->paginate(25);

        return app('view')->make('crm-data-operations-livewire::operation-browser', ['operations' => $operations]);
    }
}
