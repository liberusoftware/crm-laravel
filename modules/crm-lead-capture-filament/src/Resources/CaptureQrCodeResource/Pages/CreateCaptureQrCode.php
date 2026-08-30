<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource;

final class CreateCaptureQrCode extends CreateRecord
{
    protected static string $resource = CaptureQrCodeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()?->current_team_id;
        $data['actor_id'] = auth()->id();

        return $data;
    }
}
