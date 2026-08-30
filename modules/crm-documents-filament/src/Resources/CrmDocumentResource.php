<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\Documents\Models\CrmDocument;

final class CrmDocumentResource extends Resource
{
    protected static ?string $model = CrmDocument::class;

    protected static ?string $navigationLabel = 'CRM Documents';

    public static function getPages(): array
    {
        return [];
    }
}
