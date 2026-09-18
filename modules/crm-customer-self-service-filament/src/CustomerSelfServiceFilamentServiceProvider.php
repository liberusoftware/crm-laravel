<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\SelfServiceCaseResource;

final class CustomerSelfServiceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CustomerSelfServiceFilamentPlugin::class);
    }
}

final class CustomerSelfServiceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-customer-self-service';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SelfServiceCaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
