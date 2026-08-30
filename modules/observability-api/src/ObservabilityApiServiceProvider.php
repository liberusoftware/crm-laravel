<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityApi;

use Illuminate\Support\ServiceProvider;

final class ObservabilityApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
