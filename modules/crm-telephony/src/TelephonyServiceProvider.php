<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Telephony\Services\TelephonyAudit;

final class TelephonyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TelephonyAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
