<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging;

use Illuminate\Support\ServiceProvider;

final class MobileMessagingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
