<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource\Pages\EditWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource\Pages\ListWhiteLabelSettings;
use Liberu\CRM\WhiteLabel\Models\WhiteLabelSettings;

final class WhiteLabelSettingsResource extends Resource
{
    protected static ?string $model = WhiteLabelSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'White label';

    protected static ?string $slug = 'white-label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('brand_name')->maxLength(255), TextInput::make('custom_domain')->maxLength(255), TextInput::make('theme')->required()->maxLength(100), TextInput::make('provider')->required()->maxLength(100), Textarea::make('client_experience')->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state), Toggle::make('show_platform_attribution')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('brand_name')->placeholder('(default)'), TextColumn::make('custom_domain')->placeholder('(platform domain)'), TextColumn::make('theme'), TextColumn::make('provider')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public static function getPages(): array
    {
        return ['index' => ListWhiteLabelSettings::route('/'), 'edit' => EditWhiteLabelSettings::route('/{record}/edit')];
    }
}
