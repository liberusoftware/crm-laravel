<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CopilotFilament\Resources\CopilotRequestResource;

final class ListCopilotRequests extends ListRecords
{
    protected static string $resource = CopilotRequestResource::class;
}
