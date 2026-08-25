<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Prospecting\Filament\Resources\ProspectResource;

final class ProspectingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-prospecting';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProspectResource::class]);
    }

    public function boot(Panel $panel): void {}
}
