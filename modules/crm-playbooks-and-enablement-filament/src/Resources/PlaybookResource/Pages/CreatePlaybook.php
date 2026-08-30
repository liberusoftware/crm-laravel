<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\PlaybooksAndEnablement\Actions\CreatePlaybook as CreatePlaybookAction;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource;

final class CreatePlaybook extends CreateRecord
{
    protected static string $resource = PlaybookResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreatePlaybookAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
