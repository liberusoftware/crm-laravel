<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQApi;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CPQLivewire\Components\QuoteBuilder;
use Livewire\Livewire;

final class CpqApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if (class_exists(QuoteBuilder::class)) {
            Livewire::addNamespace('module-crm-cpq', classNamespace: 'Liberu\\CRM\\CPQLivewire\\Components');
        }
    }
}
