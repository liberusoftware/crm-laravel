<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccessFilament;

use Illuminate\Support\ServiceProvider;

final class ApiAccessFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'api-access-filament');
    }
}
