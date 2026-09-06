<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsFilament\Resources\CampaignResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Campaigns\Actions\CreateCampaign;
use Liberu\CRM\CampaignsFilament\Resources\CampaignResource;

final class CreateCampaignPage extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateCampaign::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), $data);
    }
}
