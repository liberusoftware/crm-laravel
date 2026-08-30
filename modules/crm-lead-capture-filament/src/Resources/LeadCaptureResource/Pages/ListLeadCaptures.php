<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources\LeadCaptureResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadCapture\Filament\Resources\LeadCaptureResource;

final class ListLeadCaptures extends ListRecords
{
    protected static string $resource = LeadCaptureResource::class;
}
