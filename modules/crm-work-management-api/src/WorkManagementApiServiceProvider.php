<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Api;

use Illuminate\Support\ServiceProvider;

final class WorkManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
