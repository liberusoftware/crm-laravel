<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Livewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\Telephony\Queries\TelephonyQuery;
use Livewire\Component;

final class TelephonyDashboard extends Component
{
    public function render(TelephonyQuery $query): View
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return app('view')->make('crm-telephony-livewire::dashboard', ['calls' => $query->calls((int) $id)->limit(25)->get(), 'numbers' => $query->numbers((int) $id)->get(), 'queues' => $query->queues((int) $id)->get(), 'settings' => $query->settings((int) $id)]);
    }
}
