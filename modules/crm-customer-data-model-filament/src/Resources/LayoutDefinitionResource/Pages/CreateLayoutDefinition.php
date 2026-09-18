<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource;

final class CreateLayoutDefinition extends CreateRecord
{
    protected static string $resource = LayoutDefinitionResource::class;
}
