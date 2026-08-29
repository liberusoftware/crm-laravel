<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses;

use Illuminate\Support\ServiceProvider;

final class LearningAndCoursesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
