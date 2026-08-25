<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationApi;

use Illuminate\Support\ServiceProvider;

final class DealRegistrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
