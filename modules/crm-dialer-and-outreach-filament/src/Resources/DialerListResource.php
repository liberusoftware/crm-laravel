<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;

final class DialerListResource extends Resource
{
    protected static ?string $model = DialerList::class;

    protected static ?string $navigationLabel = 'Dialer lists';

    public static function getPages(): array
    {
        return [];
    }
}
