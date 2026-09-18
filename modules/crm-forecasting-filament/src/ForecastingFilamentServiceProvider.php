<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ForecastingFilament\Resources\ForecastResource;

final class ForecastingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForecastingFilamentPlugin::class);
    }
}

final class ForecastingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-forecasting';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ForecastResource::class]);
    }

    public function boot(Panel $panel): void {}
}
