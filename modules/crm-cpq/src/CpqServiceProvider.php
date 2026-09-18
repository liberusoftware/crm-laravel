<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CPQLivewire\Components\QuoteBuilder;
use Livewire\Livewire;

final class CpqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (class_exists(QuoteBuilder::class)) {
            Livewire::addNamespace('module-crm-cpq', classNamespace: 'Liberu\\CRM\\CPQLivewire\\Components');
        }
    }
}
