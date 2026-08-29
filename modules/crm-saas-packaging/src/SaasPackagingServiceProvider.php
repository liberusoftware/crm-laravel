<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging;

use Illuminate\Support\ServiceProvider;

final class SaasPackagingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
