<?php

declare(strict_types=1);

namespace Liberu\CRM\Advocacy;

use Illuminate\Support\ServiceProvider;

final class AdvocacyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
