<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ProductWorkspaceLivewire\Livewire\ProductDashboard;
use Livewire\Livewire;

final class ProductWorkspaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-product-workspace-livewire');
        Livewire::component('module-crm-product-workspace-livewire::dashboard', ProductDashboard::class);
    }
}
