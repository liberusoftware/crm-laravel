<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceCase;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\CreateSelfServiceCase;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\EditSelfServiceCase;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\ListSelfServiceCases;

final class SelfServiceCaseResource extends Resource
{
    protected static ?string $model = SelfServiceCase::class;

    protected static ?string $navigationLabel = 'Self-service cases';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSelfServiceCases::route('/'), 'create' => CreateSelfServiceCase::route('/create'), 'edit' => EditSelfServiceCase::route('/{record}/edit')];
    }
}
