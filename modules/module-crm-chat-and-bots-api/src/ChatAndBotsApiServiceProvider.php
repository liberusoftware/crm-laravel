<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsApi;

use Illuminate\Support\ServiceProvider;

final class ChatAndBotsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
