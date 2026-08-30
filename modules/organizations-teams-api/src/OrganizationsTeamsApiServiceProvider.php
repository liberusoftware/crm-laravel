<?php

declare(strict_types=1);

namespace Liberu\Foundation\OrganizationsTeamsApi;

use Illuminate\Support\ServiceProvider;

final class OrganizationsTeamsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
