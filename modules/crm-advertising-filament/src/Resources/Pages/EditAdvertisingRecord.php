<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\AdvertisingFilament\Resources\AdvertisingRecordResource;

final class EditAdvertisingRecord extends EditRecord
{
    protected static string $resource = AdvertisingRecordResource::class;
}
