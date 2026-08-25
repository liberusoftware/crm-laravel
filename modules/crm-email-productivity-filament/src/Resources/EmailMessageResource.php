<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;

final class EmailMessageResource extends Resource
{
    protected static ?string $model = EmailMessage::class;

    protected static ?string $navigationLabel = 'Email productivity';

    public static function getPages(): array
    {
        return [];
    }
}
