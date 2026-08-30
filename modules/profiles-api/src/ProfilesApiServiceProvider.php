<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesApi;

use Illuminate\Support\ServiceProvider;

final class ProfilesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
