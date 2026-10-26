<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceApi;

use Illuminate\Support\ServiceProvider;

final class ProductWorkspaceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
