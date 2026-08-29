<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Projects\Livewire\Components\ProjectsDashboard;
use Livewire\Livewire;

final class ProjectsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-projects::dashboard', ProjectsDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-projects-livewire');
    }
}
