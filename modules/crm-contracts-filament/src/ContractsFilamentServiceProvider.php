<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsFilament;

use Illuminate\Support\ServiceProvider;

final class ContractsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ContractsFilamentPlugin::class);
    }
}
