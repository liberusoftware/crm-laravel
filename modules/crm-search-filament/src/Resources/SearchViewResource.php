<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\CrmSearch\Models\SearchView;

final class SearchViewResource extends Resource
{
    protected static ?string $model = SearchView::class;

    protected static ?string $navigationLabel = 'Saved search views';

    public static function getPages(): array
    {
        return [];
    }
}
