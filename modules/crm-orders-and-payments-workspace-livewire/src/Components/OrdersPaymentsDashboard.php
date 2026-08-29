<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\CRM\OrdersAndPaymentsWorkspace\Queries\TransactionQuery;
use Livewire\Component;

final class OrdersPaymentsDashboard extends Component
{
    public function render(): View
    {
        return app('view')->make('module-crm-orders-and-payments-workspace::dashboard', ['transactions' => app(TransactionQuery::class)->forTeam((int) auth()->user()->current_team_id)->paginate(25)]);
    }
}
