<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource;

final class ListCpqQuotes extends ListRecords
{
    protected static string $resource = CpqQuoteResource::class;
}
