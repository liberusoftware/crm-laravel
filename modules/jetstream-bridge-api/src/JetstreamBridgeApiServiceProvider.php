<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeApi;

use Illuminate\Support\ServiceProvider;

final class JetstreamBridgeApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
