<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\CustomerDataModel\Models\ObjectDefinition;
use Livewire\Component;
use Livewire\WithPagination;

final class SchemaBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $objects = ObjectDefinition::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where('label', 'like', '%'.addcslashes($this->search, '%_').'%'))->withCount('fields')->latest()->paginate(25);

        return app('view')->make('crm-customer-data-model-livewire::schema-browser', ['objects' => $objects]);
    }
}
