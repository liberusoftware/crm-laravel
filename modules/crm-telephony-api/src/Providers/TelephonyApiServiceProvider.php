<?php

declare(strict_types=1);

namespace Liberu\CRM\TelephonyApi\Providers;

use Illuminate\Support\ServiceProvider;

final class TelephonyApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
