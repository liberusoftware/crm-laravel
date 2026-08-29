<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackApi;

use Illuminate\Support\ServiceProvider;

final class AutomationPackApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
