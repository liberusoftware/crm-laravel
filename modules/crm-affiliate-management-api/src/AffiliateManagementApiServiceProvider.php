<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementApi;

use Illuminate\Support\ServiceProvider;

final class AffiliateManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
