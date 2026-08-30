<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\WorkManagement\Livewire\Components\WorkBoard;
use Livewire\Livewire;

final class WorkManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-work-management-livewire');
        Livewire::component('module-crm-work-management::work-board', WorkBoard::class);
    }
}
