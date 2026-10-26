<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessApi;

use Illuminate\Support\ServiceProvider;

final class CustomerSuccessApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
