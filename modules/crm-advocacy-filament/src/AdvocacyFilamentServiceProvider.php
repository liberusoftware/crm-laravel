<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyFilament;

use Illuminate\Support\ServiceProvider;

final class AdvocacyFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-advocacy-filament');
    }
}
