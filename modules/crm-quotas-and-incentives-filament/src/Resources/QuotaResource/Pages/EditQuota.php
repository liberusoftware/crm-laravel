<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\QuotasAndIncentives\Actions\UpdateQuota;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource;
use Liberu\CRM\QuotasAndIncentives\Models\Quota;

final class EditQuota extends EditRecord
{
    protected static string $resource = QuotaResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Quota, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateQuota::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
