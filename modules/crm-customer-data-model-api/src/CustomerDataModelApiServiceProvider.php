<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel\Api;

use Illuminate\Support\ServiceProvider;

final class CustomerDataModelApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
