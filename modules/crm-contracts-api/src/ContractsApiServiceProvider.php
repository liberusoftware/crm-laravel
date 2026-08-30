<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsApi;

use Illuminate\Support\ServiceProvider;

final class ContractsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
