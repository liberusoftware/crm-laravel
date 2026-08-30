<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class DeveloperExperienceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'developer-experience-livewire');
        Livewire::component('developer-experience-livewire-overview', Liberu\Foundation\DeveloperExperienceLivewire\Livewire\Overview::class);
    }
}
