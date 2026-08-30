<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource;

final class ListWhiteLabelSettings extends ListRecords
{
    protected static string $resource = WhiteLabelSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->url(fn () => WhiteLabelSettingsResource::getUrl('edit', ['record' => WhiteLabelSettingsResource::getEloquentQuery()->firstOrCreate(['team_id' => auth()->user()->current_team_id])->getKey()]))];
    }
}
