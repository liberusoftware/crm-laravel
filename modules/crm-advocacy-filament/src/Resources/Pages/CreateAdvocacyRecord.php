<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\AdvocacyFilament\Resources\AdvocacyRecordResource;

final class CreateAdvocacyRecord extends CreateRecord
{
    protected static string $resource = AdvocacyRecordResource::class;
}
