<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ProfilesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'profiles-livewire');
        Livewire::component('profiles-livewire-overview', Liberu\Foundation\ProfilesLivewire\Livewire\Overview::class);
    }
}
