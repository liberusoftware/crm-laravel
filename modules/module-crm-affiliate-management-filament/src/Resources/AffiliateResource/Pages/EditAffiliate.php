<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\AffiliateManagement\Models\Affiliate;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource;

final class EditAffiliate extends EditRecord
{
    protected static string $resource = AffiliateResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Affiliate, 404);
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId === (int) $record->team_id, 403);
        $record->update($data);

        return $record->refresh();
    }
}
