<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\EmailProductivityFilament\Resources\EmailMessageResource;

final class CreateEmailMessage extends CreateRecord
{
    protected static string $resource = EmailMessageResource::class;
}
