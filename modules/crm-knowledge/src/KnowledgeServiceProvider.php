<?php

declare(strict_types=1);

namespace Liberu\CRM\Knowledge;

use Illuminate\Support\ServiceProvider;

final class KnowledgeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
