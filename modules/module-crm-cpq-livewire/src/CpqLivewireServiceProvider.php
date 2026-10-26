<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class CpqLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::addNamespace('module-crm-cpq', classNamespace: 'Liberu\\CRM\\CPQLivewire\\Components');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-cpq-livewire');
    }
}
