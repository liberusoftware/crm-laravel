<?php

declare(strict_types=1);

namespace Liberu\Foundation\ModuleManagerApi;

use Illuminate\Support\ServiceProvider;

final class ModuleManagerApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
