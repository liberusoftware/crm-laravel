<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\SelfServiceCaseResource;

final class EditSelfServiceCase extends EditRecord
{
    protected static string $resource = SelfServiceCaseResource::class;
}
