<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceApi\Providers;

use Illuminate\Support\ServiceProvider;

final class OmnichannelServiceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
