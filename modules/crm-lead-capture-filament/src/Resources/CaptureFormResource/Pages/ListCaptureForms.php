<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources\CaptureFormResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureFormResource;

final class ListCaptureForms extends ListRecords
{
    protected static string $resource = CaptureFormResource::class;
}
