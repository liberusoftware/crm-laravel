<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Api;

use Illuminate\Support\ServiceProvider;

final class DataOperationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
