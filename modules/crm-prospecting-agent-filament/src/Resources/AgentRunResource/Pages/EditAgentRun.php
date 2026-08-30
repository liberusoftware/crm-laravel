<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ProspectingAgent\Actions\UpdateAgentRun;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource;
use Liberu\CRM\ProspectingAgent\Models\AgentRun;

final class EditAgentRun extends EditRecord
{
    protected static string $resource = AgentRunResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof AgentRun, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateAgentRun::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
