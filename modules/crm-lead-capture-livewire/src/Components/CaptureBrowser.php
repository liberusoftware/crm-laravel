<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\LeadCapture\Models\LeadCapture;
use Livewire\Component;
use Livewire\WithPagination;

final class CaptureBrowser extends Component
{
    use WithPagination;

    public string $search = '';

    public string $kind = '';

    public string $status = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $captures = LeadCapture::query()->where('team_id', $teamId)->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
            $term = '%'.addcslashes($this->search, '%_').'%';
            $query->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('source', 'like', $term);
        }))->when($this->kind !== '', fn ($query) => $query->where('kind', $this->kind))->when($this->status !== '', fn ($query) => $query->where('status', $this->status))->latest('captured_at')->paginate(25);

        return app('view')->make('crm-lead-capture-livewire::capture-browser', ['captures' => $captures]);
    }
}
