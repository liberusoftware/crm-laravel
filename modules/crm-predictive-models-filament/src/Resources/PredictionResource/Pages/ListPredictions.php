<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource;

final class ListPredictions extends ListRecords
{
    protected static string $resource = PredictionResource::class;
}
