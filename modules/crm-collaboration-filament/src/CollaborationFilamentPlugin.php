<?php

declare(strict_types=1);

namespace Liberu\CRM\CollaborationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CollaborationFilament\Resources\CollaborationWorkResource;

final class CollaborationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-collaboration';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CollaborationWorkResource::class]);
    }

    public function boot(Panel $panel): void {}
}
