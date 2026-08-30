<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource;

final class CreateConsentRecord extends CreateRecord
{
    protected static string $resource = ConsentRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;
        $data['actor_id'] = auth()->id();
        $data['source'] ??= 'filament';
        $data['proof'] ??= ['operator' => true];
        $data['consented_at'] ??= now();

        return $data;
    }
}
