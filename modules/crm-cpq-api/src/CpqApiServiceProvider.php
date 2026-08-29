<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQApi;

use Illuminate\Support\ServiceProvider;

final class CpqApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
