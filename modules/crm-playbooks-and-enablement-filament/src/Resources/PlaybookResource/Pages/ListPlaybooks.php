<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource;

final class ListPlaybooks extends ListRecords
{
    protected static string $resource = PlaybookResource::class;
}
