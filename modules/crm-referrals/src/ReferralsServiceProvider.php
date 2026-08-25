<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals;

use Illuminate\Support\ServiceProvider;

final class ReferralsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
