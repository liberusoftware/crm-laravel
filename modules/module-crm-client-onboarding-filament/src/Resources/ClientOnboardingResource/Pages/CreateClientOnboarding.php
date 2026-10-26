<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ClientOnboarding\Actions\StartClientOnboarding;
use Liberu\CRM\ClientOnboardingFilament\Resources\ClientOnboardingResource;

final class CreateClientOnboarding extends CreateRecord
{
    protected static string $resource = ClientOnboardingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(StartClientOnboarding::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), (string) $data['client_key'], (array) $data['intake']);
    }
}
