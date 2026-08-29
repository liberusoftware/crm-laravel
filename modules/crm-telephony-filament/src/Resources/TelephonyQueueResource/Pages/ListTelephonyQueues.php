<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources\TelephonyQueueResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyQueueResource;

final class ListTelephonyQueues extends ListRecords
{
    protected static string $resource = TelephonyQueueResource::class;
}
