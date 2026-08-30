<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource;

final class ListWorkItems extends ListRecords
{
    protected static string $resource = WorkItemResource::class;
}
