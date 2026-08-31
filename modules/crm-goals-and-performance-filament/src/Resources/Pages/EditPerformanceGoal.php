<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\GoalsAndPerformanceFilament\Resources\PerformanceGoalResource;

final class EditPerformanceGoal extends EditRecord
{
    protected static string $resource = PerformanceGoalResource::class;
}
