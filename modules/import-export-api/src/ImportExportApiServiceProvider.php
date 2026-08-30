<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportApi;

use Illuminate\Support\ServiceProvider;

final class ImportExportApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
