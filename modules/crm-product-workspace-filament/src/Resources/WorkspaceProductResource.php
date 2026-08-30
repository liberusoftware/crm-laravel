<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\ProductWorkspace\Models\WorkspaceProduct;

final class WorkspaceProductResource extends Resource
{
    protected static ?string $model = WorkspaceProduct::class;

    protected static ?string $navigationLabel = 'Product workspace';

    public static function getPages(): array
    {
        return [];
    }
}
