<?php

declare(strict_types=1);

namespace Liberu\Foundation\AuditFilament;

use Illuminate\Support\ServiceProvider;

final class AuditFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'audit-filament');
    }
}
