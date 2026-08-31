<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Forecasting\Models\Forecast;
use Liberu\CRM\ForecastingFilament\Resources\Pages\CreateForecast;
use Liberu\CRM\ForecastingFilament\Resources\Pages\EditForecast;
use Liberu\CRM\ForecastingFilament\Resources\Pages\ListForecasts;

final class ForecastResource extends Resource
{
    protected static ?string $model = Forecast::class;

    protected static ?string $navigationLabel = 'Forecasting';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListForecasts::route('/'), 'create' => CreateForecast::route('/create'), 'edit' => EditForecast::route('/{record}/edit')];
    }
}
