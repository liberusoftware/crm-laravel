<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\SaasPackaging\Actions\UpdateSubscription;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource;
use Liberu\CRM\SaasPackaging\Models\SaasSubscription;

final class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof SaasSubscription, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateSubscription::class)->execute((int) $teamId, auth()->id(), $data);
    }
}
