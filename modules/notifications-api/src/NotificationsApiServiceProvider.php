<?php

declare(strict_types=1);

namespace Liberu\Foundation\NotificationsApi;

use Illuminate\Support\ServiceProvider;

final class NotificationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
