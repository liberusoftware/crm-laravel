<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\PerformanceGoalResource;

final class CreatePerformanceGoal extends CreateRecord
{
    protected static string $resource = PerformanceGoalResource::class;
}
