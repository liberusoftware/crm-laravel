<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament;

use Illuminate\Support\ServiceProvider;

final class AttributionFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AttributionFilamentPlugin::class);
    }
}
