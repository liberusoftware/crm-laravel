<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource;

final class CaseManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-case-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
