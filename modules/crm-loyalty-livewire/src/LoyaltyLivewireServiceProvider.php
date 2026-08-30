<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyLivewire;

use Illuminate\Support\ServiceProvider;

final class LoyaltyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-loyalty');
    }
}
