<?php

namespace Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerDataModel\Filament\Resources\ObjectDefinitionResource;

final class CreateObjectDefinition extends CreateRecord
{
    protected static string $resource = ObjectDefinitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');

        return $data;
    }
}
