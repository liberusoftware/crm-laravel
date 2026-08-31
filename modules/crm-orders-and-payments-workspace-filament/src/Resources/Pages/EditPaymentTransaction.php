<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\OrdersAndPaymentsWorkspaceFilament\Resources\PaymentTransactionResource;

final class EditPaymentTransaction extends EditRecord
{
    protected static string $resource = PaymentTransactionResource::class;
}
