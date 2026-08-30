<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Filament\Resources\BookingResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Scheduling\Filament\Resources\BookingResource;

final class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;
}
