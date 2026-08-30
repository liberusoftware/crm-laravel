<?php

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource;

final class ListObjectDefinitions extends ListRecords
{
    protected static string $resource = ObjectDefinitionResource::class;
}
