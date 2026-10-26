<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\ForecastingFilament\Resources\ForecastResource;

final class EditForecast extends EditRecord
{
    protected static string $resource = ForecastResource::class;
}
