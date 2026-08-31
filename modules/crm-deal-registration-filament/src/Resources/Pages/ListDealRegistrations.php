<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\DealRegistrationFilament\Resources\DealRegistrationResource;

final class ListDealRegistrations extends ListRecords
{
    protected static string $resource = DealRegistrationResource::class;
}
