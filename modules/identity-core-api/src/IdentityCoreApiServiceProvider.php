<?php

declare(strict_types=1);

namespace Liberu\Foundation\IdentityCoreApi;

use Illuminate\Support\ServiceProvider;

final class IdentityCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
