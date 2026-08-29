<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingLivewire\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\CRM\Advertising\Actions\UpsertRecord;
use Liberu\CRM\Advertising\Models\AdvertisingRecord;
use Liberu\CRM\Advertising\Queries\AdvertisingQuery;
use Livewire\Component;

final class AdvertisingWorkspace extends Component
{
    public string $kind = '';

    public string $name = '';

    public string $status = 'draft';

    /** @var array<string, mixed> */
    public array $payload = [];

    public function save(UpsertRecord $upsert): void
    {
        $this->validate(['kind' => ['required', Rule::in(AdvertisingRecord::KINDS)], 'name' => ['required', 'string', 'max:255'], 'status' => ['required', Rule::in(AdvertisingRecord::STATUSES)], 'payload' => ['array']]);
        $upsert->execute((int) auth()->user()->current_team_id, ['kind' => $this->kind, 'name' => $this->name, 'status' => $this->status, 'payload' => $this->payload]);
        $this->reset('name', 'payload');
        $this->status = 'draft';
        $this->dispatch('advertising-record-saved');
    }

    public function render(AdvertisingQuery $query, Factory $views): View
    {
        return $views->make('crm-advertising-livewire::workspace', ['records' => $query->records((int) auth()->user()->current_team_id, $this->kind ?: null)->limit(50)->get(), 'kinds' => AdvertisingRecord::KINDS]);
    }
}
