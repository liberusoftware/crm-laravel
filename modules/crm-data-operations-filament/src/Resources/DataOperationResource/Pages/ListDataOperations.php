<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\DataOperations\Filament\Resources\DataOperationResource;

final class ListDataOperations extends ListRecords
{
    protected static string $resource = DataOperationResource::class;
}
