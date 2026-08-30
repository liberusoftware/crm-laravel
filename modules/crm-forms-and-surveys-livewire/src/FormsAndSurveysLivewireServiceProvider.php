<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysLivewire;

use Illuminate\Support\ServiceProvider;

final class FormsAndSurveysLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-forms-and-surveys');
    }
}
