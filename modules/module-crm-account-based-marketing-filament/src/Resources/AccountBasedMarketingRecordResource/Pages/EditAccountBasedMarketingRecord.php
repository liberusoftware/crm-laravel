<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AccountBasedMarketing\Actions\UpsertRecord;
use Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource;

final class EditAccountBasedMarketingRecord extends EditRecord
{
    protected static string $resource = AccountBasedMarketingRecordResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpsertRecord::class)->execute((int) auth()->user()->current_team_id, $data, (int) $record->getKey());
    }
}
