<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Api;

use Illuminate\Support\ServiceProvider;

final class ConsentAndPreferencesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
