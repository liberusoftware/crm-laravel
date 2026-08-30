<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources\CaptureReferralResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureReferralResource;

final class ListCaptureReferrals extends ListRecords
{
    protected static string $resource = CaptureReferralResource::class;
}
