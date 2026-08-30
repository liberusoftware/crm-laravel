<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TerritoriesAndOwnership\Filament\Resources\TerritoryResource;

final class TerritoriesAndOwnershipFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TerritoriesAndOwnershipFilamentPlugin::class);
    }
}

final class TerritoriesAndOwnershipFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-territories-and-ownership';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TerritoryResource::class]);
    }

    public function boot(Panel $panel): void {}
}
