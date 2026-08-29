<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\QuotasAndIncentives\Actions\CreateQuota as CreateQuotaAction;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource;

final class CreateQuota extends CreateRecord
{
    protected static string $resource = QuotaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateQuotaAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
