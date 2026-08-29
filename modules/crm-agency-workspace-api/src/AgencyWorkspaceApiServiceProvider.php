<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceApi;

use Illuminate\Support\ServiceProvider;

final class AgencyWorkspaceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
