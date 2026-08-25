<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\Telephony\Filament\Resources\TelephonyNumberResource;

final class CreateTelephonyNumber extends CreateRecord
{
    protected static string $resource = TelephonyNumberResource::class;
}
