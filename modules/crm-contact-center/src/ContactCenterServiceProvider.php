<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter;

use Illuminate\Support\ServiceProvider;

final class ContactCenterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
