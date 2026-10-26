<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AgencyWorkspaceLivewire\Components\AgencyAccountBrowser;
use Livewire\Livewire;

final class AgencyWorkspaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-agency-workspace::account-browser', AgencyAccountBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-agency-workspace-livewire');
    }
}
