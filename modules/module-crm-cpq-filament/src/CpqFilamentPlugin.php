<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource;

final class CpqFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-cpq';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CpqQuoteResource::class]);
    }

    public function boot(Panel $panel): void {}
}
