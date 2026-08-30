<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Routing\Filament\Resources\AssignmentResource;

final class RoutingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-routing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AssignmentResource::class]);
    }

    public function boot(Panel $panel): void {}
}
