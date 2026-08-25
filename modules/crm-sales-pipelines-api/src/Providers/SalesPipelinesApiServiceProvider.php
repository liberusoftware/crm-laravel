<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelinesApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SalesPipelinesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
