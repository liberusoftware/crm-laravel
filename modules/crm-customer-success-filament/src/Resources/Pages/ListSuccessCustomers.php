<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerSuccessFilament\Resources\SuccessCustomerResource;

final class ListSuccessCustomers extends ListRecords
{
    protected static string $resource = SuccessCustomerResource::class;
}
