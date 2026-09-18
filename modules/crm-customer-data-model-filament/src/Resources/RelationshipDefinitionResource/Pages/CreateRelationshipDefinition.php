<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\RelationshipDefinitionResource;

final class CreateRelationshipDefinition extends CreateRecord
{
    protected static string $resource = RelationshipDefinitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');

        return $data;
    }
}
