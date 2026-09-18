<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\DealRegistrationFilament\Resources\DealRegistrationResource;

final class CreateDealRegistration extends CreateRecord
{
    protected static string $resource = DealRegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');
        $data['owner_id'] = (int) auth()->id();

        return $data;
    }
}
