<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Communities\Actions\CreateCommunitySpace;
use Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource;

final class CreateCommunitySpacePage extends CreateRecord
{
    protected static string $resource = CommunitySpaceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateCommunitySpace::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), (string) $data['name'], (string) $data['kind'], (array) ($data['settings'] ?? []));
    }
}
