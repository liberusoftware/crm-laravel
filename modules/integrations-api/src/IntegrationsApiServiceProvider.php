<?php

declare(strict_types=1);

namespace Liberu\Foundation\IntegrationsApi;

use Illuminate\Support\ServiceProvider;

final class IntegrationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
