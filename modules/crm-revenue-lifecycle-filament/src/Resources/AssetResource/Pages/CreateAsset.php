<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\RevenueLifecycle\Actions\ManageAsset;
use Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource;

final class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(ManageAsset::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
