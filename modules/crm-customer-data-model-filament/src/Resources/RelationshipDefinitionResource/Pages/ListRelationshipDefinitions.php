<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource;

final class ListRelationshipDefinitions extends ListRecords
{
    protected static string $resource = RelationshipDefinitionResource::class;
}
