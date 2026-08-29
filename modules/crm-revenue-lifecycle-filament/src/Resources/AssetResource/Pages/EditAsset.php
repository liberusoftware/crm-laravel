<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\RevenueLifecycle\Actions\ManageAsset;
use Liberu\CRM\RevenueLifecycle\Filament\Resources\AssetResource;
use Liberu\CRM\RevenueLifecycle\Models\RevenueAsset;

final class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof RevenueAsset, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(ManageAsset::class)->execute((int) $teamId, auth()->id(), ['id' => $record->id, ...$data]);
    }
}
