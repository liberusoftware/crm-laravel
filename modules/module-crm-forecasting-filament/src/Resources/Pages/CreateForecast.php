<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\ForecastingFilament\Resources\ForecastResource;

final class CreateForecast extends CreateRecord
{
    protected static string $resource = ForecastResource::class;
}
