<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\LayoutDefinitionResource;

final class EditLayoutDefinition extends EditRecord
{
    protected static string $resource = LayoutDefinitionResource::class;
}
