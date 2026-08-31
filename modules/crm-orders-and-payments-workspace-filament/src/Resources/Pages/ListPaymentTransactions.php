<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\PaymentTransactionResource;

final class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;
}
