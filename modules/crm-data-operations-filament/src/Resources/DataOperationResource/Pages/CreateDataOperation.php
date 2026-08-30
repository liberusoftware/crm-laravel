<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource;

final class CreateDataOperation extends CreateRecord
{
    protected static string $resource = DataOperationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;
        $data['actor_id'] = auth()->id();

        return $data;
    }
}
