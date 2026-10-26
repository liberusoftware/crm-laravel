<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AccountBasedMarketingLivewire\Components\AccountBasedMarketingWorkspace;
use Livewire\Livewire;

final class AccountBasedMarketingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-account-based-marketing-livewire');
        Livewire::component('module-crm-account-based-marketing::workspace', AccountBasedMarketingWorkspace::class);
    }
}
