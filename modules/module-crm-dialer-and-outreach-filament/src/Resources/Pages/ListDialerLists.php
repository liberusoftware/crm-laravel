<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\DialerAndOutreachFilament\Resources\DialerListResource;

final class ListDialerLists extends ListRecords
{
    protected static string $resource = DialerListResource::class;
}
