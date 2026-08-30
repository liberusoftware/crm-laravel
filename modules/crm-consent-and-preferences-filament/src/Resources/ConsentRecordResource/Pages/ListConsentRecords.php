<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource;

final class ListConsentRecords extends ListRecords
{
    protected static string $resource = ConsentRecordResource::class;
}
