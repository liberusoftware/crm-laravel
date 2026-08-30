<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources\TelephonyCallResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyCallResource;

final class ListTelephonyCalls extends ListRecords
{
    protected static string $resource = TelephonyCallResource::class;
}
