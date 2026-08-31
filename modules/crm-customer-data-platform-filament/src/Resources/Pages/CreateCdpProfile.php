<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\CdpProfileResource;

final class CreateCdpProfile extends CreateRecord
{
    protected static string $resource = CdpProfileResource::class;
}
