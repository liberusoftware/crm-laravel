<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsLivewire;

use Illuminate\Support\ServiceProvider;

final class MarketingDevelopmentFundsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-marketing-development-funds');
    }
}
