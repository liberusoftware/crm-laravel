<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Filament\Resources\ReferralResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Referrals\Actions\CreateReferral as CreateReferralAction;
use Liberu\CRM\Referrals\Filament\Resources\ReferralResource;

final class CreateReferral extends CreateRecord
{
    protected static string $resource = ReferralResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateReferralAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
