<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaCaseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaCaseResource;

final class ListSlaCases extends ListRecords
{
    protected static string $resource = SlaCaseResource::class;
}
