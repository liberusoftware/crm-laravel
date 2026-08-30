<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Personalization\Livewire\Components\PersonalizationDashboard;
use Livewire\Livewire;

final class PersonalizationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-personalization::dashboard', PersonalizationDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-personalization-livewire');
    }
}
