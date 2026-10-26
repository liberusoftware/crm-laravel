<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CrmSearchFilament\Resources\SearchViewResource;

final class ListSearchViews extends ListRecords
{
    protected static string $resource = SearchViewResource::class;
}
