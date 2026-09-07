<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspaceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ProductWorkspaceFilament\Resources\WorkspaceProductResource;

final class ProductWorkspaceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductWorkspaceFilamentPlugin::class);
    }
}

final class ProductWorkspaceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-product-workspace';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WorkspaceProductResource::class]);
    }

    public function boot(Panel $panel): void {}
}
