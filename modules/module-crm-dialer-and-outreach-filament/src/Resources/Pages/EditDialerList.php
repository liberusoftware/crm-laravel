<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\DialerAndOutreachFilament\Resources\DialerListResource;

final class EditDialerList extends EditRecord
{
    protected static string $resource = DialerListResource::class;
}
