<?php

declare(strict_types=1);

namespace Liberu\Foundation\OrganizationsTeamsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class OrganizationsTeamsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'organizations-teams-livewire');
        Livewire::component('organizations-teams-livewire-overview', Liberu\Foundation\OrganizationsTeamsLivewire\Livewire\Overview::class);
    }
}
