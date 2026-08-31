<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CopilotFilament\Resources\CopilotRequestResource;

final class EditCopilotRequest extends EditRecord
{
    protected static string $resource = CopilotRequestResource::class;
}
