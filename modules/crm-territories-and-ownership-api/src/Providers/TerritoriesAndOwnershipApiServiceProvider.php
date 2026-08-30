<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnershipApi\Providers;

use Illuminate\Support\ServiceProvider;

final class TerritoriesAndOwnershipApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
