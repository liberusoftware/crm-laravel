<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningApi;

use Illuminate\Support\ServiceProvider;

final class AccountPlanningApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
