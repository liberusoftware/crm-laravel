<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\CdpProfileResource;

final class EditCdpProfile extends EditRecord
{
    protected static string $resource = CdpProfileResource::class;
}
