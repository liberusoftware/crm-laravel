<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablementApi\Providers;

use Illuminate\Support\ServiceProvider;

final class PlaybooksAndEnablementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
