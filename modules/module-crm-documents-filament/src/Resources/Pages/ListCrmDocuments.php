<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\DocumentsFilament\Resources\CrmDocumentResource;

final class ListCrmDocuments extends ListRecords
{
    protected static string $resource = CrmDocumentResource::class;
}
