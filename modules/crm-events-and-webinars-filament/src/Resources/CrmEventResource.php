<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;

final class CrmEventResource extends Resource
{
    protected static ?string $model = CrmEvent::class;

    protected static ?string $navigationLabel = 'Events and webinars';

    public static function getPages(): array
    {
        return [];
    }
}
