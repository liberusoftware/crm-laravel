<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\WorkManagement\Actions\CreateWorkQueue as CreateWorkQueueAction;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource;

final class CreateWorkQueue extends CreateRecord
{
    protected static string $resource = WorkQueueResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return app(CreateWorkQueueAction::class)->execute((int) $teamId, auth()->id(), $data);
    }
}
