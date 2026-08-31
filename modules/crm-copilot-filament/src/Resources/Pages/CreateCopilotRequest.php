<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\CopilotFilament\Resources\CopilotRequestResource;

final class CreateCopilotRequest extends CreateRecord
{
    protected static string $resource = CopilotRequestResource::class;
}
