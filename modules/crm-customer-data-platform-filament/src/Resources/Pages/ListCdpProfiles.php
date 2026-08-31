<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\CdpProfileResource;

final class ListCdpProfiles extends ListRecords
{
    protected static string $resource = CdpProfileResource::class;
}
