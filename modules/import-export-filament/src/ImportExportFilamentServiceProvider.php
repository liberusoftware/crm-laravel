<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportFilament;

use Illuminate\Support\ServiceProvider;

final class ImportExportFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'import-export-filament');
    }
}
