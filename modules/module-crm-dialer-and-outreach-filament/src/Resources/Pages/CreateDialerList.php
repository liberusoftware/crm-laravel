<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\DialerAndOutreachFilament\Resources\DialerListResource;

final class CreateDialerList extends CreateRecord
{
    protected static string $resource = DialerListResource::class;
}
