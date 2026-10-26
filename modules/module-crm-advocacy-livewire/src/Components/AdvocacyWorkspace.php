<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyLivewire\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\CRM\Advocacy\Actions\UpsertRecord;
use Liberu\CRM\Advocacy\Models\AdvocacyRecord;
use Liberu\CRM\Advocacy\Queries\AdvocacyQuery;
use Livewire\Component;

final class AdvocacyWorkspace extends Component
{
    public string $kind = '';

    public string $name = '';

    public string $status = 'draft';

    /** @var array<string, mixed> */
    public array $payload = [];

    public function save(UpsertRecord $upsert): void
    {
        $this->validate(['kind' => ['required', Rule::in(AdvocacyRecord::KINDS)], 'name' => ['required', 'string', 'max:255'], 'status' => ['required', Rule::in(AdvocacyRecord::STATUSES)], 'payload' => ['array']]);
        $upsert->execute((int) auth()->user()->current_team_id, ['kind' => $this->kind, 'name' => $this->name, 'status' => $this->status, 'payload' => $this->payload]);
        $this->reset('name', 'payload');
        $this->status = 'draft';
        $this->dispatch('advocacy-record-saved');
    }

    public function render(AdvocacyQuery $query, Factory $views): View
    {
        return $views->make('crm-advocacy-livewire::workspace', ['records' => $query->records((int) auth()->user()->current_team_id, $this->kind ?: null)->limit(50)->get(), 'kinds' => AdvocacyRecord::KINDS]);
    }
}
