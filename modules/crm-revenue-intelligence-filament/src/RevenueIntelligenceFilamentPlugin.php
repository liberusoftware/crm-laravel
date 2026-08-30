<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\RevenueIntelligence\Filament\Resources\InsightResource;

final class RevenueIntelligenceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-revenue-intelligence';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([InsightResource::class]);
    }

    public function boot(Panel $panel): void {}
}
