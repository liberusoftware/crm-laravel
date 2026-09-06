<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource;

final class ListCases extends ListRecords
{
    protected static string $resource = CaseResource::class;
}
