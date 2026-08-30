<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Api;

use Illuminate\Support\ServiceProvider;

final class WebIntentApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
