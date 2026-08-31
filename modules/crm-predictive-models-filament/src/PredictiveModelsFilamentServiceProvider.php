<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament;

use Illuminate\Support\ServiceProvider;

final class PredictiveModelsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PredictiveModelsFilamentPlugin::class);
    }
}
