<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class LandingPagesAndFunnelsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
