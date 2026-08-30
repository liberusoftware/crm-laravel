<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Api;

use Illuminate\Support\ServiceProvider;

final class ActivitiesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
