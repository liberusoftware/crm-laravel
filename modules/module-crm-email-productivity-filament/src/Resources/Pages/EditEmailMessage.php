<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\EmailProductivityFilament\Resources\EmailMessageResource;

final class EditEmailMessage extends EditRecord
{
    protected static string $resource = EmailMessageResource::class;
}
