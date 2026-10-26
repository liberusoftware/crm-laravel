<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerSuccessFilament\Resources\SuccessCustomerResource;

final class EditSuccessCustomer extends EditRecord
{
    protected static string $resource = SuccessCustomerResource::class;
}
