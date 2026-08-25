<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningLivewire\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanning\Models\AccountPlanningRecord;
use Liberu\CRM\AccountPlanning\Queries\AccountPlanningQuery;
use Livewire\Component;

final class AccountPlanningWorkspace extends Component
{
    public string $kind = '';

    public string $name = '';

    public string $status = 'draft';

    /** @var array<string, mixed> */
    public array $payload = [];

    public function save(UpsertRecord $upsert): void
    {
        $this->validate(['kind' => ['required', Rule::in(AccountPlanningRecord::KINDS)], 'name' => ['required', 'string', 'max:255'], 'status' => ['required', Rule::in(AccountPlanningRecord::STATUSES)], 'payload' => ['array']]);
        $upsert->execute((int) auth()->user()->current_team_id, ['kind' => $this->kind, 'name' => $this->name, 'status' => $this->status, 'payload' => $this->payload]);
        $this->reset('name', 'payload');
        $this->status = 'draft';
        $this->dispatch('account-planning-record-saved');
    }

    public function render(AccountPlanningQuery $query, Factory $views): View
    {
        return $views->make('crm-account-planning-livewire::workspace', ['records' => $query->records((int) auth()->user()->current_team_id, $this->kind ?: null)->limit(50)->get(), 'kinds' => AccountPlanningRecord::KINDS]);
    }
}
