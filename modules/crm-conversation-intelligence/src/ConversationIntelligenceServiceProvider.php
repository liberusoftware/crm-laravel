<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligence;

use Illuminate\Support\ServiceProvider;

final class ConversationIntelligenceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
