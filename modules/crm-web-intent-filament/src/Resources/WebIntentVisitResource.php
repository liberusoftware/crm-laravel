<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\WebIntent\Filament\Resources\WebIntentVisitResource\Pages\ListWebIntentVisits;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;

final class WebIntentVisitResource extends Resource
{
    protected static ?string $model = WebIntentVisit::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Web intent visits';

    protected static ?string $slug = 'web-intent-visits';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('visitor_key')->disabled(), TextInput::make('intent_level')->disabled(), TextInput::make('score')->disabled()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('visitor_key')->searchable(), TextColumn::make('intent_level')->badge(), TextColumn::make('score')->sortable(), TextColumn::make('consent_status')->badge(), TextColumn::make('started_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListWebIntentVisits::route('/')];
    }
}
