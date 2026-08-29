<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlementsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class SlaAndEntitlementsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
