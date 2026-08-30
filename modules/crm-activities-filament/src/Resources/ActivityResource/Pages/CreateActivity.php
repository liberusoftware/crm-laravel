<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource;

final class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()?->current_team_id;
        $data['actor_id'] = auth()->id();

        return $data;
    }
}
