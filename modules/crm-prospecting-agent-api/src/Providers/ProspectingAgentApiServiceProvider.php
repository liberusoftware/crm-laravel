<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgentApi\Providers;

use Illuminate\Support\ServiceProvider;

final class ProspectingAgentApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
