<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization;

use Illuminate\Support\ServiceProvider;

final class PersonalizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
