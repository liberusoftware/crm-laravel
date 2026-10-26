<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AdvocacyLivewire\Components\AdvocacyWorkspace;
use Livewire\Livewire;

final class AdvocacyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-advocacy-livewire');
        Livewire::component('module-crm-advocacy::workspace', AdvocacyWorkspace::class);
    }
}
