<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource;

final class ListTelephonyNumbers extends ListRecords
{
    protected static string $resource = TelephonyNumberResource::class;
}
