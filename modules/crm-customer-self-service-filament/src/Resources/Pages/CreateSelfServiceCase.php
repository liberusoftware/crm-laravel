<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\SelfServiceCaseResource;

final class CreateSelfServiceCase extends CreateRecord
{
    protected static string $resource = SelfServiceCaseResource::class;
}
