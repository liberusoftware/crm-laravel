<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsApi;

use Illuminate\Support\ServiceProvider;

final class EventsAndWebinarsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
