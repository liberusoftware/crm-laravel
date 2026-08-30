<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\Forecasting\Models\Forecast;

final class ForecastResource extends Resource
{
    protected static ?string $model = Forecast::class;

    protected static ?string $navigationLabel = 'Forecasting';

    public static function getPages(): array
    {
        return [];
    }
}
