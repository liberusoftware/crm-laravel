<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Filament;

use Illuminate\Support\ServiceProvider;

final class TelephonyFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TelephonyFilamentPlugin::class);
    }
}
