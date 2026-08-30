<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Api;

use Illuminate\Support\ServiceProvider;

final class CRMCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
