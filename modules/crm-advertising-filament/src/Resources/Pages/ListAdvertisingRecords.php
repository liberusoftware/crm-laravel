<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AdvertisingFilament\Resources\AdvertisingRecordResource;

final class ListAdvertisingRecords extends ListRecords
{
    protected static string $resource = AdvertisingRecordResource::class;
}
