<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DealRegistrationFilament\Resources\DealRegistrationResource;

final class DealRegistrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DealRegistrationFilamentPlugin::class);
    }
}

final class DealRegistrationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-deal-registration';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([DealRegistrationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
