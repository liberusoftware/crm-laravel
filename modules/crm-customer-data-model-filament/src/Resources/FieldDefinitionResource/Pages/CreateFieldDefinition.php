<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource;

final class CreateFieldDefinition extends CreateRecord
{
    protected static string $resource = FieldDefinitionResource::class;
}
