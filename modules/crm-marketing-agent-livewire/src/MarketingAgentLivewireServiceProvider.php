<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentLivewire;

use Illuminate\Support\ServiceProvider;

final class MarketingAgentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-marketing-agent');
    }
}
