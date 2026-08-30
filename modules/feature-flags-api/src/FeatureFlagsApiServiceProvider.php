<?php

declare(strict_types=1);

namespace Liberu\Foundation\FeatureFlagsApi;

use Illuminate\Support\ServiceProvider;

final class FeatureFlagsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
