<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource;

final class ListBookings extends ListRecords
{
    protected static string $resource = PlanningResource::class;
}
