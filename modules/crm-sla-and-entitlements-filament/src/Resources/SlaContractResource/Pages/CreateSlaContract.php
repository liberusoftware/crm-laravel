<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource;

final class CreateSlaContract extends CreateRecord
{
    protected static string $resource = SlaContractResource::class;
}
