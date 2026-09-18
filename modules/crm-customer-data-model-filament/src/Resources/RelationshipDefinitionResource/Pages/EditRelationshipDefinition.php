<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource;

final class EditRelationshipDefinition extends EditRecord
{
    protected static string $resource = RelationshipDefinitionResource::class;
}
