<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingApi;

use Illuminate\Support\ServiceProvider;

final class EmailMarketingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
