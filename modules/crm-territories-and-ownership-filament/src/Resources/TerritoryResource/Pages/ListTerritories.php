<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Filament\Resources\TerritoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\TerritoriesAndOwnership\Filament\Resources\TerritoryResource;

final class ListTerritories extends ListRecords
{
    protected static string $resource = TerritoryResource::class;
}
