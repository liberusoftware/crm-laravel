<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Activities\Livewire\Components\ActivityBrowser;
use Liberu\CRM\Activities\Livewire\Components\ActivityForm;
use Livewire\Livewire;

final class ActivitiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-activities-livewire');
        Livewire::component('module-crm-activities::activity-browser', ActivityBrowser::class);
        Livewire::component('module-crm-activities::activity-form', ActivityForm::class);
    }
}
