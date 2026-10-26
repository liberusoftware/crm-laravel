<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource;

final class ListProcesses extends ListRecords
{
    protected static string $resource = ProcessResource::class;
}
