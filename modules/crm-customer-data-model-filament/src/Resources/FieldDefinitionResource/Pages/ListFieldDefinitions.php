<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource;

final class ListFieldDefinitions extends ListRecords
{
    protected static string $resource = FieldDefinitionResource::class;
}
