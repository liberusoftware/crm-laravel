<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Core\Enums\RecordType;
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
        $type = RecordType::tryFrom($this->type);
        abort_unless($type !== null, 422, 'The record type is not supported.');
        $search = trim($this->search);
        $records = Record::query()->where('team_id', (int) $teamId)->where('record_type', $type->value)->active()->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.addcslashes($search, '%_').'%'))->latest()->paginate(25);

        return app('view')->make('crm-core-livewire::record-browser', ['records' => $records]);
    }
}
