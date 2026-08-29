<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing;

use Illuminate\Support\ServiceProvider;

final class EmailMarketingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
