<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ConsentAndPreferences\Livewire\Components\ConsentBrowser;
use Livewire\Livewire;

final class ConsentAndPreferencesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-consent-and-preferences-livewire');
        Livewire::component('module-crm-consent-and-preferences::consent-browser', ConsentBrowser::class);
    }
}
