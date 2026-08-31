<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Filament;

use Illuminate\Support\ServiceProvider;

final class SaasPackagingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SaasPackagingFilamentPlugin::class);
    }
}
