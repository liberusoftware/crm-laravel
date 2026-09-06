<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\CaseManagement\Actions\OpenCase;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource;

final class CreateCase extends CreateRecord
{
    protected static string $resource = CaseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(OpenCase::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), $data);
    }
}
