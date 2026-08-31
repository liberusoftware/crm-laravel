<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AccountBasedMarketing\Actions\UpsertRecord;
use Liberu\CRM\AccountBasedMarketingFilament\Resources\AccountBasedMarketingRecordResource;

final class CreateAccountBasedMarketingRecord extends CreateRecord
{
    protected static string $resource = AccountBasedMarketingRecordResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(UpsertRecord::class)->execute((int) auth()->user()->current_team_id, $data);
    }
}
