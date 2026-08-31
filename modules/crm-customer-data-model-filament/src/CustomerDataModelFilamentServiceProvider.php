<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Filament;

use Illuminate\Support\ServiceProvider;

final class CustomerDataModelFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CustomerDataModelFilamentPlugin::class);
    }
}
