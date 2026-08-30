<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccessApi;

use Illuminate\Support\ServiceProvider;

final class ApiAccessApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
