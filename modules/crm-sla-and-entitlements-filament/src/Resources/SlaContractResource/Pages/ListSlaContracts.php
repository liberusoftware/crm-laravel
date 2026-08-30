<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource;

final class ListSlaContracts extends ListRecords
{
    protected static string $resource = SlaContractResource::class;
}
