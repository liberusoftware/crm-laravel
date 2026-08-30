<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvocacyApi;

use Illuminate\Support\ServiceProvider;

final class AdvocacyApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
