<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ProspectingAgent\Actions\CreateAgentRun as CreateAgentRunAction;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource;

final class CreateAgentRun extends CreateRecord
{
    protected static string $resource = AgentRunResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateAgentRunAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
