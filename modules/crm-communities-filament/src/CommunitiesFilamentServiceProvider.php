<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesFilament;

use Illuminate\Support\ServiceProvider;

final class CommunitiesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommunitiesFilamentPlugin::class);
    }
}
