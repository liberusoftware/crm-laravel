<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ServiceAgent\Filament\Resources\AgentCaseResource;

final class ServiceAgentFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-service-agent';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AgentCaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
