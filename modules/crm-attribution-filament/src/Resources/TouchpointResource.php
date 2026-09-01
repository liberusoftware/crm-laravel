<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Attribution\Models\Touchpoint;
use Liberu\CRM\AttributionFilament\Resources\TouchpointResource\Pages\CreateTouchpoint;
use Liberu\CRM\AttributionFilament\Resources\TouchpointResource\Pages\ListTouchpoints;

final class TouchpointResource extends Resource
{
    protected static ?string $model = Touchpoint::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('visitor_key')->required()->maxLength(255),
            TextInput::make('source')->required()->maxLength(120),
            TextInput::make('medium')->maxLength(120),
            TextInput::make('campaign')->maxLength(180),
            TextInput::make('click_id')->maxLength(255),
            TextInput::make('channel')->maxLength(80),
            TextInput::make('cost')->numeric()->minValue(0),
            KeyValue::make('metadata')->json(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('visitor_key')->searchable(),
            TextColumn::make('source')->badge()->sortable(),
            TextColumn::make('medium'),
            TextColumn::make('campaign')->searchable(),
            TextColumn::make('cost')->money('USD'),
            TextColumn::make('occurred_at')->dateTime()->sortable(),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->getAttribute('current_team_id');
        abort_unless(is_numeric($teamId) && (int) $teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', (int) $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListTouchpoints::route('/'), 'create' => CreateTouchpoint::route('/create')];
    }
}
