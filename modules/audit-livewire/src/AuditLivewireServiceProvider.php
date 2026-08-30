<?php

declare(strict_types=1);

namespace Liberu\Foundation\AuditLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AuditLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'audit-livewire');
        Livewire::component('audit-livewire-overview', Liberu\Foundation\AuditLivewire\Livewire\Overview::class);
    }
}
