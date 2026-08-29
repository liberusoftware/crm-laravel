<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspaceApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SalesWorkspaceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
