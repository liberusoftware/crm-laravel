<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadCaptureFilament\Resources\CapturedLeadResource;

final class ListCapturedLeads extends ListRecords
{
    protected static string $resource = CapturedLeadResource::class;
}
