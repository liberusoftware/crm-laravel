<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CPQLivewire\Components\QuoteBuilder;
use Livewire\Livewire;

final class CpqLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-cpq::quote-builder', QuoteBuilder::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-cpq-livewire');
    }
}
