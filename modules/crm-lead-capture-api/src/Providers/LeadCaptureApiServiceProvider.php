<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureApi\Providers;

use Illuminate\Support\ServiceProvider;

final class LeadCaptureApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
