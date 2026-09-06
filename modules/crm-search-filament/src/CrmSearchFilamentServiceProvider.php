<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CrmSearchFilament\Resources\SearchViewResource;

final class CrmSearchFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CrmSearchFilamentPlugin::class);
    }
}

final class CrmSearchFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-search';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SearchViewResource::class]);
    }

    public function boot(Panel $panel): void {}
}
