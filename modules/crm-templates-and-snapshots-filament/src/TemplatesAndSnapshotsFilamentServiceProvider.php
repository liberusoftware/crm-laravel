<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource;

final class TemplatesAndSnapshotsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TemplatesAndSnapshotsFilamentPlugin::class);
    }
}

final class TemplatesAndSnapshotsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-templates-and-snapshots';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SnapshotResource::class]);
    }

    public function boot(Panel $panel): void {}
}
