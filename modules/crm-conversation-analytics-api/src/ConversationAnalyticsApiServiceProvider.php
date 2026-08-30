<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalyticsApi;

use Illuminate\Support\ServiceProvider;

final class ConversationAnalyticsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
