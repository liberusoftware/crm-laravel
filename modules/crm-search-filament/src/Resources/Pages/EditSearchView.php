<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\CrmSearchFilament\Resources\SearchViewResource;

final class EditSearchView extends EditRecord
{
    protected static string $resource = SearchViewResource::class;
}
