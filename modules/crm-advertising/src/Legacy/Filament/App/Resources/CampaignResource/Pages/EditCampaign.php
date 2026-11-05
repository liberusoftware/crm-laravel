<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CampaignResource\Pages;

use App\Filament\App\Resources\CampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['metadata'] = array_replace($this->record->getAttribute('metadata') ?? [], array_intersect_key($data['metadata'] ?? [], array_flip(['ad_copy', 'headline', 'call_to_action'])));
        if (filled($this->record->getAttribute('external_id'))) {
            $data['external_id'] = $this->record->getAttribute('external_id');
            $data['status'] = $this->record->getAttribute('status');
        }

        return $data;
    }
}
