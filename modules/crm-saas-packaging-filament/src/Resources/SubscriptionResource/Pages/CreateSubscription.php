<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\SaasPackaging\Actions\ProvisionSubscription;
use Liberu\CRM\SaasPackaging\Filament\Resources\SubscriptionResource;

final class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ProvisionSubscription::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
