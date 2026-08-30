<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService;

use Illuminate\Support\ServiceProvider;

final class OmnichannelServiceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
