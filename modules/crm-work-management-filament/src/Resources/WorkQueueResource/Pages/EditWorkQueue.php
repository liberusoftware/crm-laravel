<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\WorkManagement\Actions\UpdateWorkQueue;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource;
use Liberu\CRM\WorkManagement\Models\WorkQueue;

final class EditWorkQueue extends EditRecord
{
    protected static string $resource = WorkQueueResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof WorkQueue, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateWorkQueue::class)->execute($record, auth()->id(), $data);
    }
}
