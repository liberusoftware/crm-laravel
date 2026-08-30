<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversationsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class UnifiedConversationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
