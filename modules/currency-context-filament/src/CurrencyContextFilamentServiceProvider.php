<?php

declare(strict_types=1);

namespace Liberu\Foundation\CurrencyContextFilament;

use Illuminate\Support\ServiceProvider;

final class CurrencyContextFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'currency-context-filament');
    }
}
