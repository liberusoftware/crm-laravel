<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerSuccessFilament\Resources\SuccessCustomerResource;

final class CreateSuccessCustomer extends CreateRecord
{
    protected static string $resource = SuccessCustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');

        return $data;
    }
}
