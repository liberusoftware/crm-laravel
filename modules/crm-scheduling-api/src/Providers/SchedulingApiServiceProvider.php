<?php

declare(strict_types=1);

namespace Liberu\CRM\SchedulingApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SchedulingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
