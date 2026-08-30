<?php

declare(strict_types=1);

namespace Liberu\CRM\ProjectsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class ProjectsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
