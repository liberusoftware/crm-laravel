<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament\Resources\ConversionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AttributionFilament\Resources\ConversionResource;

final class ListConversions extends ListRecords
{
    protected static string $resource = ConversionResource::class;
}
