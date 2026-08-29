<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\PlaybooksAndEnablement\Filament\Resources\PlaybookResource;

final class PlaybooksAndEnablementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-playbooks-and-enablement';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PlaybookResource::class]);
    }

    public function boot(Panel $panel): void {}
}
