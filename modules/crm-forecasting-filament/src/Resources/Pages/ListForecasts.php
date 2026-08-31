<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ForecastingFilament\Resources\ForecastResource;

final class ListForecasts extends ListRecords
{
    protected static string $resource = ForecastResource::class;
}
