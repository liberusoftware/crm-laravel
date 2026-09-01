<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Collaboration\Actions\AssignCollaborationWork;
use Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource;

final class CreateCollaborationWork extends CreateRecord
{
    protected static string $resource = CollaborationWorkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(AssignCollaborationWork::class)->execute((int) $user?->getAttribute('current_team_id'), (string) $data['queue_key'], (string) $data['subject_key'], isset($data['assignee_key']) ? (string) $data['assignee_key'] : null, (array) ($data['metadata'] ?? []));
    }
}
