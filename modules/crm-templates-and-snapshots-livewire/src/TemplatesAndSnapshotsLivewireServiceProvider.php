<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TemplatesAndSnapshots\Livewire\Components\SnapshotDashboard;
use Livewire\Livewire;

final class TemplatesAndSnapshotsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-templates-and-snapshots::dashboard', SnapshotDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-templates-and-snapshots-livewire');
    }
}
