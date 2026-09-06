<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Attribution\Models\Conversion;
use Liberu\CRM\AttributionFilament\Resources\ConversionResource\Pages\ListConversions;

final class ConversionResource extends Resource
{
    protected static ?string $model = Conversion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('visitor_key')->disabled(),
            TextInput::make('conversion_key')->disabled(),
            Select::make('model')->options(['first_touch' => 'First touch', 'last_touch' => 'Last touch', 'linear' => 'Linear', 'multi_touch' => 'Multi-touch'])->disabled(),
            TextInput::make('value')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('visitor_key')->searchable(),
            TextColumn::make('conversion_key')->searchable(),
            TextColumn::make('model')->badge(),
            TextColumn::make('value')->money('USD'),
            TextColumn::make('converted_at')->dateTime()->sortable(),
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
        return ['index' => ListConversions::route('/')];
    }
}
