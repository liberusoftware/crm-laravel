<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingLivewire\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\CRM\AccountBasedMarketing\Actions\UpsertRecord;
use Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord;
use Liberu\CRM\AccountBasedMarketing\Queries\AccountBasedMarketingQuery;
use Livewire\Component;

final class AccountBasedMarketingWorkspace extends Component
{
    public string $kind = '';

    public string $name = '';

    public string $status = 'draft';

    /** @var array<string, mixed> */
    public array $payload = [];

    public function save(UpsertRecord $upsert): void
    {
        abort_unless((int) auth()->user()?->current_team_id > 0, 403);

        $this->validate([
            'kind' => ['required', Rule::in(AccountBasedMarketingRecord::KINDS)],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(AccountBasedMarketingRecord::STATUSES)],
            'payload' => ['array'],
        ]);

        $upsert->execute((int) auth()->user()->current_team_id, [
            'kind' => $this->kind,
            'name' => $this->name,
            'status' => $this->status,
            'payload' => $this->payload,
        ]);
        $this->reset('name', 'payload');
        $this->status = 'draft';
        $this->dispatch('abm-record-saved');
    }

    public function render(AccountBasedMarketingQuery $query, Factory $views): View
    {
        abort_unless((int) auth()->user()?->current_team_id > 0, 403);

        return $views->make('crm-account-based-marketing-livewire::workspace', [
            'records' => $query->records((int) auth()->user()->current_team_id, $this->kind ?: null)->limit(50)->get(),
            'kinds' => AccountBasedMarketingRecord::KINDS,
        ]);
    }
}
