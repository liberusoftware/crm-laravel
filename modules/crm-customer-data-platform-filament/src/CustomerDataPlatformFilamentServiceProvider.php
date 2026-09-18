<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerDataPlatformFilament\Resources\CdpProfileResource;

final class CustomerDataPlatformFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CustomerDataPlatformFilamentPlugin::class);
    }
}

final class CustomerDataPlatformFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-customer-data-platform';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CdpProfileResource::class]);
    }

    public function boot(Panel $panel): void {}
}
