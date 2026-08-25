<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagementApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SalesEngagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
