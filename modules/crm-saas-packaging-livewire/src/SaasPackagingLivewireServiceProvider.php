<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SaasPackaging\Livewire\Components\SaasDashboard;
use Livewire\Livewire;

final class SaasPackagingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-saas-packaging::dashboard', SaasDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-saas-packaging-livewire');
    }
}
