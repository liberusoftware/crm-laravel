<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\BusinessProcessManagement\Actions\CreateProcess;
use Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource;

final class CreateProcessPage extends CreateRecord
{
    protected static string $resource = ProcessResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateProcess::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), $data);
    }
}
