<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace\Queries;

use Liberu\CRM\OrdersAndPaymentsWorkspace\Models\PaymentTransaction;

final class TransactionQuery
{
    public function forTeam(int $teamId)
    {
        return PaymentTransaction::query()->where('team_id', $teamId)->with('events')->latest();
    }
}
