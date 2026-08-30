<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Core\Livewire\Components\RecordBrowser;
use Livewire\Livewire;

final class CRMCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-core-livewire');
        Livewire::component('module-crm-core::record-browser', RecordBrowser::class);
    }
}
