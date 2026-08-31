<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Filament;

use Illuminate\Support\ServiceProvider;

final class SalesPipelinesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SalesPipelinesFilamentPlugin::class);
    }
}
