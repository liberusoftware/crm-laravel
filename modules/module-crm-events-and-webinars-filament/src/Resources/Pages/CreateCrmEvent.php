<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\CrmEventResource;

final class CreateCrmEvent extends CreateRecord
{
    protected static string $resource = CrmEventResource::class;
}
