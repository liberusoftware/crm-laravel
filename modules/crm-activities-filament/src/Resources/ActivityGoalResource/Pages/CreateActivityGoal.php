<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource;

final class CreateActivityGoal extends CreateRecord
{
    protected static string $resource = ActivityGoalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = auth()->user()?->current_team_id;
        $data['owner_id'] = auth()->id();

        return $data;
    }
}
