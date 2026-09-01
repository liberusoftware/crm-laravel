<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationFilament;

use Illuminate\Support\ServiceProvider;

final class CollaborationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CollaborationFilamentPlugin::class);
    }
}
