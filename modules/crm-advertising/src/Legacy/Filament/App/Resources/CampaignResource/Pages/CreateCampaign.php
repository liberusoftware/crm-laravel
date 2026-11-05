<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CampaignResource\Pages;

use App\Filament\App\Resources\CampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'paused';
        $data['metadata'] = array_intersect_key($data['metadata'] ?? [], array_flip(['ad_copy', 'headline', 'call_to_action']));

        return $data;
    }
}
