<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\DealRegistrationFilament\Resources\DealRegistrationResource;

final class CreateDealRegistration extends CreateRecord
{
    protected static string $resource = DealRegistrationResource::class;
}
