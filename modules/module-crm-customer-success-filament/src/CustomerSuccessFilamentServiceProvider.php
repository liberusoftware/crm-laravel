<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerSuccessFilament\Resources\SuccessCustomerResource;

final class CustomerSuccessFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CustomerSuccessFilamentPlugin::class);
    }
}

final class CustomerSuccessFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-customer-success';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SuccessCustomerResource::class]);
    }

    public function boot(Panel $panel): void {}
}
