<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource;

final class ListWorkQueues extends ListRecords
{
    protected static string $resource = WorkQueueResource::class;
}
