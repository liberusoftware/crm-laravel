<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation;

use Illuminate\Support\ServiceProvider;

final class AIReceptionAndConversationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
