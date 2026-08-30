<?php

declare(strict_types=1);

namespace Liberu\Foundation\LocalizationCoreApi;

use Illuminate\Support\ServiceProvider;

final class LocalizationCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
