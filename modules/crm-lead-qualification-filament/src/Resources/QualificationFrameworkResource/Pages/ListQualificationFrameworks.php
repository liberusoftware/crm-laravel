<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadQualification\Filament\Resources\QualificationFrameworkResource;

final class ListQualificationFrameworks extends ListRecords
{
    protected static string $resource = QualificationFrameworkResource::class;
}
