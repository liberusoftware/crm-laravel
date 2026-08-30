<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnelsLivewire;

use Illuminate\Support\ServiceProvider;

final class LandingPagesAndFunnelsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-landing-pages-and-funnels');
    }
}
