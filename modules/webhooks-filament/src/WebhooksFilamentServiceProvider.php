<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksFilament;

use Illuminate\Support\ServiceProvider;

final class WebhooksFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'webhooks-filament');
    }
}
