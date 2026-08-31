<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\CrmEventResource;

final class ListCrmEvents extends ListRecords
{
    protected static string $resource = CrmEventResource::class;
}
