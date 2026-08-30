<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource;

final class ListCaptureQrCodes extends ListRecords
{
    protected static string $resource = CaptureQrCodeResource::class;
}
