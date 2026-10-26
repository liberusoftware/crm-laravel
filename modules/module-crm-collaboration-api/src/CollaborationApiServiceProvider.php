<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationApi;

use Illuminate\Support\ServiceProvider;

final class CollaborationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
