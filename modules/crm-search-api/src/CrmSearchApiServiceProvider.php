<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchApi;

use Illuminate\Support\ServiceProvider;

final class CrmSearchApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
