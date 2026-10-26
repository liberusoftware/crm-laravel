<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AttributionLivewire\Components\AttributionBrowser;
use Livewire\Livewire;

final class AttributionLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-attribution::attribution-browser', AttributionBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-attribution-livewire');
    }
}
