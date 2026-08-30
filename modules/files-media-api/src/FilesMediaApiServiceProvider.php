<?php

declare(strict_types=1);

namespace Liberu\Foundation\FilesMediaApi;

use Illuminate\Support\ServiceProvider;

final class FilesMediaApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
