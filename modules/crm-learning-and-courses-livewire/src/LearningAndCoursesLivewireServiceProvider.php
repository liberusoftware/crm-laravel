<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesLivewire;

use Illuminate\Support\ServiceProvider;

final class LearningAndCoursesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-learning-and-courses');
    }
}
