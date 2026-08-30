<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureFormResource;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureReferralResource;
use Liberu\CRM\LeadCapture\Filament\Resources\LeadCaptureResource;

final class LeadCaptureFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-lead-capture';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([LeadCaptureResource::class, CaptureFormResource::class, CaptureQrCodeResource::class, CaptureReferralResource::class]);
    }

    public function boot(Panel $panel): void {}
}
