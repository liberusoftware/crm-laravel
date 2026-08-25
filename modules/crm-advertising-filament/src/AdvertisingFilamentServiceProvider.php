<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingFilament;

use Illuminate\Support\ServiceProvider;

final class AdvertisingFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-advertising-filament');
    }
}
