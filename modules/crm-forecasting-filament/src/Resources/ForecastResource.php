<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\Forecasting\Models\ForecastCategory;
use Liberu\CRM\ForecastingFilament\Resources\Pages\CreateForecast;
use Liberu\CRM\ForecastingFilament\Resources\Pages\EditForecast;
use Liberu\CRM\ForecastingFilament\Resources\Pages\ListForecasts;

final class ForecastResource extends Resource
{
    protected static ?string $model = Forecast::class;

    protected static ?string $navigationLabel = 'Forecasting';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->label('Forecast category')->options(fn (): array => ForecastCategory::query()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'))->where('active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(),
            TextInput::make('period')->placeholder('2026-Q4')->required()->maxLength(40),
            Select::make('scenario')->options(['base' => 'Base', 'upside' => 'Upside', 'downside' => 'Downside'])->default('base')->required(),
            TextInput::make('pipeline')->numeric()->minValue(0)->default(0)->required(),
            TextInput::make('best_case')->label('Best case')->numeric()->minValue(0)->default(0)->required(),
            TextInput::make('commit')->label('Commit')->numeric()->minValue(0)->default(0)->required(),
            TextInput::make('coverage')->numeric()->minValue(0)->default(0)->helperText('Pipeline coverage ratio for this forecast period.'),
            KeyValue::make('metadata')->json()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('category.name')->label('Category')->searchable(),
            TextColumn::make('period')->sortable(),
            TextColumn::make('scenario')->badge(),
            TextColumn::make('pipeline')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('best_case')->label('Best case')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('commit')->numeric(decimalPlaces: 2)->sortable(),
            TextColumn::make('coverage')->numeric(decimalPlaces: 2),
        ])->defaultSort('period', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListForecasts::route('/'), 'create' => CreateForecast::route('/create'), 'edit' => EditForecast::route('/{record}/edit')];
    }
}
