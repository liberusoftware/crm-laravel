<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversations;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\UnifiedConversations\Services\ConversationAudit;
use Liberu\CRM\UnifiedConversations\Services\ConversationPolicy;

final class UnifiedConversationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConversationPolicy::class);
        $this->app->singleton(ConversationAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
