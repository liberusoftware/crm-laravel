<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\WhiteLabel\Filament\Resources\WhiteLabelSettingsResource;

final class WhiteLabelFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'module-crm-white-label-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WhiteLabelSettingsResource::class]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new self();
    }
}
