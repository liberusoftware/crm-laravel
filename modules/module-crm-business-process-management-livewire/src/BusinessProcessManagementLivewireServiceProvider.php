<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\BusinessProcessManagementLivewire\Components\ProcessBrowser;
use Livewire\Livewire;

final class BusinessProcessManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-business-process-management::process-browser', ProcessBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-business-process-management');
    }
}
