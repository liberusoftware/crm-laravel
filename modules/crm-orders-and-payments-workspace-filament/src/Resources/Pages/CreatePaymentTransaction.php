<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\PaymentTransactionResource;

final class CreatePaymentTransaction extends CreateRecord
{
    protected static string $resource = PaymentTransactionResource::class;
}
