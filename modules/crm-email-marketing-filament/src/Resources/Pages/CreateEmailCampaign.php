<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\EmailMarketingFilament\Resources\EmailCampaignResource;

final class CreateEmailCampaign extends CreateRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()?->getAttribute('current_team_id');
        $data['owner_id'] = (int) auth()->id();

        return $data;
    }
}
