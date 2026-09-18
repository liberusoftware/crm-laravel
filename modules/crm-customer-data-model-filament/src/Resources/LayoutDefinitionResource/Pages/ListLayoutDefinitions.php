<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource;

final class ListLayoutDefinitions extends ListRecords
{
    protected static string $resource = LayoutDefinitionResource::class;
}
