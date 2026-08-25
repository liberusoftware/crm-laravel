<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SalesPipelines\Livewire\Components\PipelineBoard;
use Livewire\Livewire;

final class SalesPipelinesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-sales-pipelines::board', PipelineBoard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-sales-pipelines-livewire');
    }
}
