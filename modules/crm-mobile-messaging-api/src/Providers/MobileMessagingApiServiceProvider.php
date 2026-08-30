<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingApi\Providers;

use Illuminate\Support\ServiceProvider;

final class MobileMessagingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
