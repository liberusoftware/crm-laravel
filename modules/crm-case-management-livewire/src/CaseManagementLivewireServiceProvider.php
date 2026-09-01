<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CaseManagementLivewire\Components\CaseBrowser;
use Livewire\Livewire;

final class CaseManagementLivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Livewire::component('crm-case-management::case-browser', CaseBrowser::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-case-management');
    }
}
