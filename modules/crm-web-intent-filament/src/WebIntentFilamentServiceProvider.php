<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Filament;

use Illuminate\Support\ServiceProvider;

final class WebIntentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WebIntentFilamentPlugin::class);
    }
}
