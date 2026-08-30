<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Core\Filament\Resources\RecordResource;

final class CRMCoreFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([RecordResource::class]);
    }

    public function boot(Panel $panel): void {}
}
