<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationApi;

use Illuminate\Support\ServiceProvider;

final class ApplicationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
