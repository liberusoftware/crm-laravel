<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessagingLivewire;

use Illuminate\Support\ServiceProvider;

final class MobileMessagingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-mobile-messaging');
    }
}
