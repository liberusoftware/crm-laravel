<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Segmentation\Livewire\Components\AudienceDashboard;
use Livewire\Livewire;

final class SegmentationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-segmentation::dashboard', AudienceDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-segmentation-livewire');
    }
}
