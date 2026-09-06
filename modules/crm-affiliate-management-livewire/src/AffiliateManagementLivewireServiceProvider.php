<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AffiliateManagementLivewire\Components\AffiliateBrowser;
use Livewire\Livewire;

final class AffiliateManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-affiliate-management::affiliate-browser', AffiliateBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-affiliate-management-livewire');
    }
}
