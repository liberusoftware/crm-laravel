<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Referrals\Actions\UpdateReferral;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource;
use Liberu\CRM\Referrals\Models\Referral;

final class EditReferral extends EditRecord
{
    protected static string $resource = ReferralResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Referral, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateReferral::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
