<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DataOperations\Livewire\Components\OperationBrowser;
use Livewire\Livewire;

final class DataOperationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-data-operations-livewire');
        Livewire::component('module-crm-data-operations::operation-browser', OperationBrowser::class);
    }
}
