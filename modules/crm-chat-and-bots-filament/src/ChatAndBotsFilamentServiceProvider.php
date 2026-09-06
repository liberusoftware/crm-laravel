<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsFilament;

use Illuminate\Support\ServiceProvider;

final class ChatAndBotsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChatAndBotsFilamentPlugin::class);
    }
}
