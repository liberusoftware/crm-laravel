<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentAlertResource\Pages\ListWebIntentAlerts;
use Liberu\CRM\WebIntent\Models\WebIntentAlert;

final class WebIntentAlertResource extends Resource
{
    protected static ?string $model = WebIntentAlert::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Web intent alerts';

    protected static ?string $slug = 'web-intent-alerts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('visitor_key')->disabled(), TextInput::make('title')->disabled(), TextInput::make('severity')->disabled(), TextInput::make('status')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('visitor_key')->searchable(), TextColumn::make('title')->searchable(), TextColumn::make('severity')->badge(), TextColumn::make('status')->badge(), TextColumn::make('triggered_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListWebIntentAlerts::route('/')];
    }
}
