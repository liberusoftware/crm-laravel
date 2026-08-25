<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ProspectingAgent\Filament\Resources\AgentRunResource;

final class ProspectingAgentFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-prospecting-agent';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AgentRunResource::class]);
    }

    public function boot(Panel $panel): void {}
}
