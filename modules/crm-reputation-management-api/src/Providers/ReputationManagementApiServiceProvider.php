<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagementApi\Providers;

use Illuminate\Support\ServiceProvider;

final class ReputationManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
