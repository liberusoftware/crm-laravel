<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EmailProductivityFilament\Resources\EmailMessageResource;

final class EmailProductivityFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailProductivityFilamentPlugin::class);
    }
}

final class EmailProductivityFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-email-productivity';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([EmailMessageResource::class]);
    }

    public function boot(Panel $panel): void {}
}
