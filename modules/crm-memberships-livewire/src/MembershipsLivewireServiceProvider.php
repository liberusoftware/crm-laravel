<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsLivewire;

use Illuminate\Support\ServiceProvider;

final class MembershipsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-memberships');
    }
}
