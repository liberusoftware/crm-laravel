<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationApi;

use Illuminate\Support\ServiceProvider;

final class FieldServiceCoordinationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
