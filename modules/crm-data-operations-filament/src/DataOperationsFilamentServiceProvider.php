<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Filament;

use Illuminate\Support\ServiceProvider;

final class DataOperationsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DataOperationsFilamentPlugin::class);
    }
}
