<?php

declare(strict_types=1);

namespace Liberu\Foundation\SettingsApi;

use Illuminate\Support\ServiceProvider;

final class SettingsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
