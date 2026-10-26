<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\DealRegistrationFilament\Resources\DealRegistrationResource;

final class EditDealRegistration extends EditRecord
{
    protected static string $resource = DealRegistrationResource::class;
}
