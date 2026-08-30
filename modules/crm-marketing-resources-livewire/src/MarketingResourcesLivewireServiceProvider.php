<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResourcesLivewire;

use Illuminate\Support\ServiceProvider;

final class MarketingResourcesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-marketing-resources');
    }
}
