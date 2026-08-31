<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\EmailProductivityFilament\Resources\EmailMessageResource;

final class ListEmailMessages extends ListRecords
{
    protected static string $resource = EmailMessageResource::class;
}
