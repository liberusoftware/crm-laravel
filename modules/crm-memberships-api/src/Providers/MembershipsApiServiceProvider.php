<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class MembershipsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
