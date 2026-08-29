<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Referrals\Queries\ReferralQuery;
use Livewire\Component;

final class ReferralsDashboard extends Component
{
    public function render(ReferralQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-referrals-livewire::dashboard', ['programs' => $query->programs((int) $id)->get(), 'referrals' => $query->referrals((int) $id)->limit(25)->get(), 'rewards' => $query->rewards((int) $id)->limit(25)->get()]);
    }
}
