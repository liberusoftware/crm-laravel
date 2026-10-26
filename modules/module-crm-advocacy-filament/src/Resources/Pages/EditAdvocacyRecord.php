<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\AdvocacyFilament\Resources\AdvocacyRecordResource;

final class EditAdvocacyRecord extends EditRecord
{
    protected static string $resource = AdvocacyRecordResource::class;
}
