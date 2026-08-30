<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\Copilot\Models\CopilotRequest;

final class CopilotRequestResource extends Resource
{
    protected static ?string $model = CopilotRequest::class;

    protected static ?string $navigationLabel = 'CRM Copilot';

    public static function getPages(): array
    {
        return [];
    }
}
