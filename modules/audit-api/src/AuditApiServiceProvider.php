<?php

declare(strict_types=1);

namespace Liberu\Foundation\AuditApi;

use Illuminate\Support\ServiceProvider;

final class AuditApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
