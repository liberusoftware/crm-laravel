<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachLivewire\Livewire;

use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\DialerQuery;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;
use Livewire\Component;

final class DialerDashboard extends Component
{
    public function setStatus(int $listId, string $status): void
    {
        $user = auth()->user();
        $teamId = (int) $user?->getAttribute('current_team_id');
        abort_unless($user && app(DialerPolicy::class)->canManage($teamId, (int) $user->id), 403);
        abort_unless(in_array($status, ['active', 'paused'], true), 422);
        DialerList::query()->where('team_id', $teamId)->findOrFail($listId)->update(['status' => $status]);
    }

    public function render()
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        return app('view')->make('module-crm-dialer-and-outreach-livewire::dashboard', ['lists' => app(DialerQuery::class)->lists($teamId)->limit(25)->get()]);
    }
}
