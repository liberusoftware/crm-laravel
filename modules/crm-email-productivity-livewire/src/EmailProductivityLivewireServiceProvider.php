<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EmailProductivityLivewire\Livewire\EmailDashboard;
use Livewire\Livewire;

final class EmailProductivityLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-email-productivity-livewire');
        Livewire::component('module-crm-email-productivity-livewire::dashboard', EmailDashboard::class);
    }
}
