<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\UsageWalletAndRebilling\Queries\UsageWalletQuery;
use Livewire\Component;

final class UsageWalletDashboard extends Component
{
    public function render(UsageWalletQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-usage-wallet-and-rebilling-livewire::dashboard', ['summary' => $query->summary((int) $id)]);
    }
}
