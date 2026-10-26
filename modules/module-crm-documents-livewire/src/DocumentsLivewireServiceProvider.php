<?php

declare(strict_types=1);

namespace Liberu\CRM\DocumentsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DocumentsLivewire\Livewire\DocumentsDashboard;
use Livewire\Livewire;

final class DocumentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-documents-livewire');
        Livewire::component('module-crm-documents-livewire::dashboard', DocumentsDashboard::class);
    }
}
