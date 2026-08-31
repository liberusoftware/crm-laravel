<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\LeadCaptureFilament\Resources\CapturedLeadResource;

final class EditCapturedLead extends EditRecord
{
    protected static string $resource = CapturedLeadResource::class;
}
