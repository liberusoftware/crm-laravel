<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ServiceAnalytics\Filament\Resources\AnalyticsSnapshotResource;

final class ServiceAnalyticsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-service-analytics';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AnalyticsSnapshotResource::class]);
    }

    public function boot(Panel $panel): void {}
}
