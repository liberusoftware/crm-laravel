<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ImportExportLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'import-export-livewire');
        Livewire::component('import-export-livewire-overview', Liberu\Foundation\ImportExportLivewire\Livewire\Overview::class);
    }
}
