<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBots;

use Illuminate\Support\ServiceProvider;

final class ChatAndBotsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
