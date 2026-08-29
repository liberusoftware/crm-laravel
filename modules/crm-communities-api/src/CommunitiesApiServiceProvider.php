<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesApi;

use Illuminate\Support\ServiceProvider;

final class CommunitiesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
