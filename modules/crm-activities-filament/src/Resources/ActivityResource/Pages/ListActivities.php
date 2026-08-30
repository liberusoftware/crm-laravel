<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource;

final class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;
}
