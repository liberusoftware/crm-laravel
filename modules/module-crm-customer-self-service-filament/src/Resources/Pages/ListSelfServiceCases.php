<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\SelfServiceCaseResource;

final class ListSelfServiceCases extends ListRecords
{
    protected static string $resource = SelfServiceCaseResource::class;
}
