<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingApi;

use Illuminate\Support\ServiceProvider;

final class ClientOnboardingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
