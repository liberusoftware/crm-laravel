<?php

declare(strict_types=1);

namespace Liberu\Foundation\SearchFilament;

use Illuminate\Support\ServiceProvider;

final class SearchFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'search-filament');
    }
}
