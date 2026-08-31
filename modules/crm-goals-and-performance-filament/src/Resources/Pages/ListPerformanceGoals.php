<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\PerformanceGoalResource;

final class ListPerformanceGoals extends ListRecords
{
    protected static string $resource = PerformanceGoalResource::class;
}
