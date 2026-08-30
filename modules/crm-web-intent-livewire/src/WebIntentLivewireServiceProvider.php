<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\WebIntent\Livewire\Components\IntentDashboard;
use Livewire\Livewire;

final class WebIntentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-web-intent-livewire');
        Livewire::component('module-crm-web-intent::dashboard', IntentDashboard::class);
    }
}
