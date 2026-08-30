<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalytics;

use Illuminate\Support\ServiceProvider;

final class ConversationAnalyticsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
