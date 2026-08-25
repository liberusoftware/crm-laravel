<?php

declare(strict_types=1);

namespace Liberu\CRM\PersonalizationApi\Providers;

use Illuminate\Support\ServiceProvider;

final class PersonalizationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
