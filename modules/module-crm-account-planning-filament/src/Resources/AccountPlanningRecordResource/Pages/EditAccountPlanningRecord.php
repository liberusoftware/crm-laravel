<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource;

final class EditAccountPlanningRecord extends EditRecord
{
    protected static string $resource = AccountPlanningRecordResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpsertRecord::class)->execute((int) auth()->user()->current_team_id, $data, (int) $record->getKey());
    }
}
