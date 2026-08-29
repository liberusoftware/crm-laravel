<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationApi\Providers;

use Illuminate\Support\ServiceProvider;

final class LeadQualificationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
