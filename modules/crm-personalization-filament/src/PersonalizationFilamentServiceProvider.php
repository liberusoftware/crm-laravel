<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament;

use Illuminate\Support\ServiceProvider;

final class PersonalizationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PersonalizationFilamentPlugin::class);
    }
}
