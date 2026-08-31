<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LeadCaptureFilament\Resources\CapturedLeadResource;

final class CreateCapturedLead extends CreateRecord
{
    protected static string $resource = CapturedLeadResource::class;
}
