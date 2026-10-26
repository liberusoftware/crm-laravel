<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\AdvertisingFilament\Resources\AdvertisingRecordResource;

final class CreateAdvertisingRecord extends CreateRecord
{
    protected static string $resource = AdvertisingRecordResource::class;
}
