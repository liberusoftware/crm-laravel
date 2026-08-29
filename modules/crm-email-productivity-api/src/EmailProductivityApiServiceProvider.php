<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityApi;

use Illuminate\Support\ServiceProvider;

final class EmailProductivityApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
