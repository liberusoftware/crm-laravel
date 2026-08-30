<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceLivewire;

use Illuminate\Support\ServiceProvider;

final class OmnichannelServiceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-omnichannel-service');
    }
}
