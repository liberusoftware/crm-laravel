<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerSuccessFilament\Resources\SuccessCustomerResource;

final class CreateSuccessCustomer extends CreateRecord
{
    protected static string $resource = SuccessCustomerResource::class;
}
