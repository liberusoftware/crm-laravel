<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksApi;

use Illuminate\Support\ServiceProvider;

final class WebhooksApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
