<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesApi\Providers;

use Illuminate\Support\ServiceProvider;

final class LearningAndCoursesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
