<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament;

use Illuminate\Support\ServiceProvider;

final class CpqFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(CpqFilamentPlugin::class);
    }
}
