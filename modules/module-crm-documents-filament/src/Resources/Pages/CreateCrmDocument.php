<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\DocumentsFilament\Resources\CrmDocumentResource;

final class CreateCrmDocument extends CreateRecord
{
    protected static string $resource = CrmDocumentResource::class;
}
