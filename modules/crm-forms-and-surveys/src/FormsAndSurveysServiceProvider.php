<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveys;

use Illuminate\Support\ServiceProvider;

final class FormsAndSurveysServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
