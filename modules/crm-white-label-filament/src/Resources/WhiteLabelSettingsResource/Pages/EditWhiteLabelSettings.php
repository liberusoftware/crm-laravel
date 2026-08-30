<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\WhiteLabel\Actions\UpdateWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource;

final class EditWhiteLabelSettings extends EditRecord
{
    protected static string $resource = WhiteLabelSettingsResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpdateWhiteLabelSettings::class)->execute((int) $record->getAttribute('team_id'), (int) auth()->id(), $data, (int) $record->getAttribute('version'));
    }
}
