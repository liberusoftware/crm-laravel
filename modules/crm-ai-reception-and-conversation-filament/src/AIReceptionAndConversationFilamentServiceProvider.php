<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationFilament;

use Illuminate\Support\ServiceProvider;

final class AIReceptionAndConversationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIReceptionAndConversationFilamentPlugin::class);
    }
}
