<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Api;

use Illuminate\Support\ServiceProvider;

final class WhiteLabelApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
