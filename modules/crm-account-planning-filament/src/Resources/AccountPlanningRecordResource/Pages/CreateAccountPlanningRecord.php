<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AccountPlanning\Actions\UpsertRecord;
use Liberu\CRM\AccountPlanningFilament\Resources\AccountPlanningRecordResource;

final class CreateAccountPlanningRecord extends CreateRecord
{
    protected static string $resource = AccountPlanningRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(UpsertRecord::class)->execute((int) auth()->user()->current_team_id, $data);
    }
}
