<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\EventsAndWebinarsFilament\Resources\CrmEventResource;

final class EditCrmEvent extends EditRecord
{
    protected static string $resource = CrmEventResource::class;
}
