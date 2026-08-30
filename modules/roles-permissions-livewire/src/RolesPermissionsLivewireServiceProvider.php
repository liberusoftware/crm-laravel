<?php

declare(strict_types=1);

namespace Liberu\Foundation\RolesPermissionsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class RolesPermissionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'roles-permissions-livewire');
        Livewire::component('roles-permissions-livewire-overview', Liberu\Foundation\RolesPermissionsLivewire\Livewire\Overview::class);
    }
}
