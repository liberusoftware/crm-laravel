<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagementApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SandboxAndReleaseManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
