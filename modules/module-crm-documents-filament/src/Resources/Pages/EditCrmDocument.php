<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\DocumentsFilament\Resources\CrmDocumentResource;

final class EditCrmDocument extends EditRecord
{
    protected static string $resource = CrmDocumentResource::class;
}
