<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerDataModel\Livewire\Components\SchemaBrowser;
use Livewire\Livewire;

final class CustomerDataModelLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-customer-data-model-livewire');
        Livewire::component('module-crm-customer-data-model::schema-browser', SchemaBrowser::class);
    }
}
