<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachApi;

use Illuminate\Support\ServiceProvider;

final class DialerAndOutreachApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
