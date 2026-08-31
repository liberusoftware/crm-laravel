<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LandingPagesAndFunnels\Models\Funnel;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages\CreateFunnel;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages\EditFunnel;
use Liberu\CRM\LandingPagesAndFunnelsFilament\Resources\Pages\ListFunnels;

final class FunnelResource extends Resource
{
    protected static ?string $model = Funnel::class;

    protected static ?string $navigationLabel = 'Funnels';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListFunnels::route('/'), 'create' => CreateFunnel::route('/create'), 'edit' => EditFunnel::route('/{record}/edit')];
    }
}
