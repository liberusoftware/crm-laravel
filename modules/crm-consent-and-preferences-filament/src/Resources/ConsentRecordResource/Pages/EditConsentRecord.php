<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource;

final class EditConsentRecord extends EditRecord
{
    protected static string $resource = ConsentRecordResource::class;
}
