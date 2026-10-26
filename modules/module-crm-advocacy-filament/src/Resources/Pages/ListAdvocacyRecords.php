<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AdvocacyFilament\Resources\AdvocacyRecordResource;

final class ListAdvocacyRecords extends ListRecords
{
    protected static string $resource = AdvocacyRecordResource::class;
}
