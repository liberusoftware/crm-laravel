<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Prospecting\Actions\ImportProspect;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource;

final class CreateProspect extends CreateRecord
{
    protected static string $resource = ProspectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ImportProspect::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
