<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesApi;

use Illuminate\Support\ServiceProvider;

final class SchedulerQueuesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
