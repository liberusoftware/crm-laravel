<?php

declare(strict_types=1);

namespace Liberu\CRM\Collaboration;

use Illuminate\Support\ServiceProvider;

final class CollaborationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
