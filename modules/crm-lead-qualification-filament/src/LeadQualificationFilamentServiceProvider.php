<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\LeadQualification\Filament\LeadQualificationFilamentPlugin;

final class LeadQualificationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LeadQualificationFilamentPlugin::class);
    }
}
