<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament\Resources\TouchpointResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\AttributionFilament\Resources\TouchpointResource;

final class ListTouchpoints extends ListRecords
{
    protected static string $resource = TouchpointResource::class;
}
