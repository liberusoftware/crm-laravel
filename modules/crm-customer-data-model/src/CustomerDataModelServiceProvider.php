<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataModel;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerDataModel\Contracts\SchemaValidator;
use Liberu\CRM\CustomerDataModel\Services\SchemaValidationService;

final class CustomerDataModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SchemaValidator::class, SchemaValidationService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
