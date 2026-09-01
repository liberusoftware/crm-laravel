<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsFilament\Resources\ContractResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\Contracts\Actions\CreateContract;
use Liberu\CRM\ContractsFilament\Resources\ContractResource;

final class CreateContractPage extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(CreateContract::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), $data);
    }
}
