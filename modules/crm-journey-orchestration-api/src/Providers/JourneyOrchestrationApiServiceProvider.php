<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationApi\Providers;

use Illuminate\Support\ServiceProvider;

final class JourneyOrchestrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
