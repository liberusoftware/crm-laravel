<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CrmSearchFilament\Resources\SearchViewResource;

final class CreateSearchView extends CreateRecord
{
    protected static string $resource = SearchViewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [...$data, 'team_id' => (int) auth()->user()?->getAttribute('current_team_id'), 'user_id' => (int) auth()->id()];
    }
}
