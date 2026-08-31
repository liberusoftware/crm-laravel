<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadQualificationFilament\Resources\QualifiedLeadResource;

final class ListQualifiedLeads extends ListRecords
{
    protected static string $resource = QualifiedLeadResource::class;
}
