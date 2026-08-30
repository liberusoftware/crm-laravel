<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace;

use Illuminate\Support\ServiceProvider;

final class ProductWorkspaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
