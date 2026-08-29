<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Filament\Resources\AssignmentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Routing\Actions\AssignSubject;
use Liberu\CRM\Routing\Filament\Resources\AssignmentResource;

final class CreateAssignment extends CreateRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(AssignSubject::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
