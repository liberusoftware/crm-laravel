<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModelsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class PredictiveModelsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
