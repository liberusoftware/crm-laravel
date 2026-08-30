<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadQualification\Filament\Resources\LeadQualificationResource;

final class ListLeadQualifications extends ListRecords
{
    protected static string $resource = LeadQualificationResource::class;
}
