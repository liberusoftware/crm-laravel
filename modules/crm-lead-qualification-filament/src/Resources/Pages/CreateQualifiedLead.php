<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LeadQualificationFilament\Resources\QualifiedLeadResource;

final class CreateQualifiedLead extends CreateRecord
{
    protected static string $resource = QualifiedLeadResource::class;
}
