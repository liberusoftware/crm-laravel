<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AutomationPackFilament\Resources\AutomationRecipeResource;

final class AutomationPackFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AutomationPackFilamentPlugin::class);
    }
}

final class AutomationPackFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-automation-pack';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AutomationRecipeResource::class]);
    }

    public function boot(Panel $panel): void {}
}
