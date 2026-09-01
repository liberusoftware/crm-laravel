<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource;

final class EditCase extends EditRecord
{
    protected static string $resource = CaseResource::class;
}
