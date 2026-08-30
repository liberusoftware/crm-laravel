<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource;

final class ListActivityGoals extends ListRecords
{
    protected static string $resource = ActivityGoalResource::class;
}
