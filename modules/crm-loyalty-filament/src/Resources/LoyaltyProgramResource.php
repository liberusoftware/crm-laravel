<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Loyalty\Models\LoyaltyProgram;
use Liberu\CRM\LoyaltyFilament\Resources\Pages\CreateLoyaltyProgram;
use Liberu\CRM\LoyaltyFilament\Resources\Pages\EditLoyaltyProgram;
use Liberu\CRM\LoyaltyFilament\Resources\Pages\ListLoyaltyPrograms;

final class LoyaltyProgramResource extends Resource
{
    protected static ?string $model = LoyaltyProgram::class;

    protected static ?string $navigationLabel = 'Loyalty';

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
        return ['index' => ListLoyaltyPrograms::route('/'), 'create' => CreateLoyaltyProgram::route('/create'), 'edit' => EditLoyaltyProgram::route('/{record}/edit')];
    }
}
