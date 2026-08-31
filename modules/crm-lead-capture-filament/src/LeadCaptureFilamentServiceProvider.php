<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\LeadCapture\Filament\LeadCaptureFilamentPlugin;

final class LeadCaptureFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LeadCaptureFilamentPlugin::class);
    }
}
