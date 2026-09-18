<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\FieldDefinitionResource;

final class EditFieldDefinition extends EditRecord
{
    protected static string $resource = FieldDefinitionResource::class;
}
