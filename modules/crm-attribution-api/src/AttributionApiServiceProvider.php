<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionApi;

use Illuminate\Support\ServiceProvider;

final class AttributionApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
