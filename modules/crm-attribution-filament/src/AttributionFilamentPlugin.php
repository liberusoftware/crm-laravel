<?php

declare(strict_types=1);

namespace Liberu\CRM\AttributionFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AttributionFilament\Resources\ConversionResource;
use Liberu\CRM\AttributionFilament\Resources\TouchpointResource;

final class AttributionFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-attribution';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([TouchpointResource::class, ConversionResource::class]);
    }

    public function boot(Panel $panel): void {}
}
